<?php

namespace App\Http\Controllers;

use App\Models\FuelOrder;
use App\Models\FuelType;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FuelOrderItem;
use App\Models\Supplier;
use App\Models\Carrier;





class FuelOrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = FuelOrder::with(['station', 'creator', 'items.fuelType'])->latest();

        if ($user->station_id) {
            $query->where('station_id', $user->station_id);
        } elseif ($user->company_id) {
            $query->whereHas('station', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
        }

        $orders = $query->get();

        return view('fuel_orders.index', compact('orders'));
    }

    public function create()
    {
        $this->authorizeByPermission('create_fuel_orders');

        $fuelTypes = FuelType::orderBy('name')->get();

        return view('fuel_orders.create', compact('fuelTypes'));
    }

    public function store(Request $request)
    {
        $this->authorizeByPermission('create_fuel_orders');

        $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = auth()->user();

        if (!$user->station_id) {
            return back()->withInput()->with('error', 'لا يوجد محطة مرتبطة بهذا المستخدم');
        }

        $validItems = collect($request->quantities)
            ->filter(fn ($qty) => (float) $qty > 0);

        if ($validItems->isEmpty()) {
            return back()->withInput()->with('error', 'يجب إدخال كمية أكبر من صفر لنوع وقود واحد على الأقل');
        }

        DB::transaction(function () use ($user, $request, $validItems, &$order) {
            $order = FuelOrder::create([
                'order_number' => $this->generateOrderNumber(),
                'station_id' => $user->station_id,
                'created_by' => $user->id,
                'status' => 'submitted',
                'current_step' => 'operations_review',
                'request_date' => now()->toDateString(),
                'notes' => $request->notes,
                'submitted_at' => now(),
            ]);

            foreach ($validItems as $fuelTypeId => $qty) {
                $order->items()->create([
                    'fuel_type_id' => $fuelTypeId,
                    'requested_quantity' => $qty,
                    'approved_quantity' => null,
                    'received_quantity' => null,
                ]);
            }

            $order->logs()->create([
                'user_id' => $user->id,
                'action' => 'submitted',
                'from_step' => 'station_supervisor',
                'to_step' => 'operations_review',
                'notes' => 'تم إنشاء وإرسال طلب الوقود',
            ]);
        });

        return redirect()
            ->route('fuel-orders.show', $order->id)
            ->with('success', 'تم إنشاء طلب الوقود بنجاح');
    }

    public function show($id)
    {
        $order = FuelOrder::with([
            'station',
            'creator',
            'items.fuelType',
            'logs.user',
            'transport.supplier',
            'transport.carrier',
            'finance',
        ])->findOrFail($id);

        $this->ensureUserCanViewOrder($order);

        $suppliers = Supplier::with('fuelPrices')->where('is_active', 1)->get();
        $carriers = Carrier::where('is_active', 1)->get();

        return view('fuel_orders.show', compact('order', 'suppliers', 'carriers'));
    }

    public function receiving()
    {
        $this->authorizeByPermission('receive_fuel_orders');

        $orders = FuelOrder::with(['station', 'items.fuelType'])
            ->where('current_step', 'station_receipt')
            ->latest()
            ->get();

        return view('fuel_orders.receiving', compact('orders'));
    }

    public function receivingEdit($id)
    {
        $this->authorizeByPermission('receive_fuel_orders');

        $order = FuelOrder::with(['items.fuelType', 'station.tanks'])
            ->findOrFail($id);

        if (!$order->canBeReceived()) {
            return redirect()
                ->route('fuel-orders.show', $order->id)
                ->with('error', 'هذا الطلب غير جاهز للاستلام');
        }

        return view('fuel_orders.receiving_edit', compact('order'));
    }

    public function receivingUpdate(Request $request, $id)
    {
        $this->authorizeByPermission('receive_fuel_orders');

        $order = FuelOrder::with(['items.fuelType', 'station.tanks'])
            ->findOrFail($id);

        if (!$order->canBeReceived()) {
            return back()->with('error', 'لا يمكن استلام هذا الطلب بحالته الحالية');
        }

        $request->validate([
            'received_quantities' => ['required', 'array'],
            'received_quantities.*' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $order) {
            foreach ($order->items as $item) {
                if ($item->status !== 'approved') {
                    continue;
                }

                $receivedQty = (float) ($request->received_quantities[$item->id] ?? 0);

                if ($receivedQty > (float) $item->approved_quantity) {
                    throw new \Exception("الكمية المستلمة أكبر من الكمية المعتمدة لنوع الوقود: {$item->fuelType->name}");
                }

                $item->update([
                    'received_quantity' => $receivedQty,
                    'status' => $receivedQty > 0 ? 'received' : $item->status,
                ]);

                if ($receivedQty <= 0) {
                    continue;
                }

                $tank = $order->station->tanks
                    ->where('fuel_type_id', $item->fuel_type_id)
                    ->first();

                if (!$tank) {
                    throw new \Exception("لا يوجد خزان مرتبط بنوع الوقود: {$item->fuelType->name}");
                }

                $tank->increment('current_quantity', $receivedQty);

                StockMovement::create([
                    'station_tank_id' => $tank->id,
                    'station_id' => $order->station_id,
                    'fuel_type_id' => $item->fuel_type_id,
                    'movement_type' => StockMovement::TYPE_PURCHASE,
                    'quantity' => $receivedQty,
                    'movement_date' => now()->toDateString(),
                    'reference_type' => FuelOrder::class,
                    'reference_id' => $order->id,
                    'notes' => 'استلام وقود من طلب رقم ' . $order->order_number,
                ]);
            }

            $order->update([
                'status' => 'completed',
                'current_step' => 'completed',
                'completed_at' => now(),
            ]);

            $order->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'order_received',
                'from_step' => 'station_receipt',
                'to_step' => 'completed',
                'notes' => $request->notes ?? 'تم استلام الوقود وتحديث المخزون',
            ]);
        });

        return redirect()
            ->route('fuel-orders.show', $order->id)
            ->with('success', 'تم استلام الوقود وتحديث المخزون بنجاح');
    }

    public function approveItem(Request $request, $id)
    {
        $request->validate([
            'approved_quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $item = FuelOrderItem::with('fuelOrder')->findOrFail($id);

        if ($item->status !== 'pending') {
            return back()->with('error', 'هذا الصنف تم التعامل معه مسبقًا');
        }

        $approvedQty = (float) $request->approved_quantity;

        if ($approvedQty > (float) $item->requested_quantity) {
            return back()->with('error', 'الكمية المعتمدة لا يمكن أن تكون أكبر من الكمية المطلوبة');
        }

        $item->update([
            'status' => 'approved',
            'approved_quantity' => $approvedQty,
            'rejection_reason' => null,
        ]);

        $item->fuelOrder->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'item_approved',
            'from_step' => $item->fuelOrder->current_step,
            'to_step' => $item->fuelOrder->current_step,
            'notes' => 'تم اعتماد بند بكمية ' . $approvedQty,
        ]);

        $this->updateOrderStatus($item->fuel_order_id);

        return back()->with('success', 'تم اعتماد نوع الوقود بالكمية المحددة');
    }

    public function rejectItem(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $item = \App\Models\FuelOrderItem::with('fuelOrder')->findOrFail($id);

        if ($item->status !== 'pending') {
            return back()->with('error', 'هذا الصنف تم التعامل معه مسبقًا');
        }

        $item->update([
            'status' => 'rejected',
            'approved_quantity' => 0,
            'rejection_reason' => $request->rejection_reason,
        ]);

        $item->fuelOrder->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'item_rejected',
            'from_step' => $item->fuelOrder->current_step,
            'to_step' => $item->fuelOrder->current_step,
            'notes' => 'تم رفض أحد بنود الطلب: ' . $request->rejection_reason,
        ]);

        $this->updateOrderStatus($item->fuel_order_id);

        return back()->with('success', 'تم رفض نوع الوقود');
    }

    private function updateOrderStatus($orderId): void
    {
        $order = FuelOrder::with(['items', 'finance'])->findOrFail($orderId);

        $total = $order->items->count();

        if ($total === 0) {
            return;
        }

        $approved = $order->items->where('status', 'approved')->count();
        $rejected = $order->items->where('status', 'rejected')->count();

        $oldStatus = $order->status;
        $oldStep = $order->current_step;

        if ($rejected === $total) {
            $newStatus = 'rejected';
            $newStep = null;
        } elseif ($approved + $rejected === $total) {
            $newStatus = 'approved';
            $newStep = 'finance_review';
        } else {
            return;
        }

        $order->update([
            'status' => $newStatus,
            'current_step' => $newStep,
        ]);

        // إنشاء سجل المالية فقط عند التحويل إلى مرحلة المالية
        if ($newStep === 'finance_review' && !$order->finance) {
            $order->finance()->create([
                'status' => 'pending',
            ]);
        }

        if ($oldStatus !== $newStatus || $oldStep !== $newStep) {
            $order->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'status_updated',
                'from_step' => $oldStep,
                'to_step' => $newStep,
                'notes' => 'تم تحديث حالة الطلب إلى ' . $newStatus,
            ]);
        }
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastId = (FuelOrder::max('id') ?? 0) + 1;

        return 'FO-' . $date . '-' . str_pad((string) $lastId, 5, '0', STR_PAD_LEFT);
    }

    private function authorizeByPermission(string $permissionSlug): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermission($permissionSlug)) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذا الإجراء');
        }
    }

    private function ensureUserCanViewOrder(FuelOrder $order): void
    {
        $user = auth()->user();

        if ($user->hasPermission('view_all_fuel_orders')) {
            return;
        }

        if ($user->station_id && (int) $user->station_id === (int) $order->station_id) {
            return;
        }

        if ($user->company_id && (int) $user->company_id === (int) $order->company_id) {
            return;
        }

        abort(403, 'ليس لديك صلاحية لعرض هذا الطلب');
    }
}