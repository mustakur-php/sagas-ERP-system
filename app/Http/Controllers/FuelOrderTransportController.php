<?php

namespace App\Http\Controllers;

use App\Models\FuelOrder;
use App\Models\FuelOrderTransport;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelOrderTransportController extends Controller
{
    public function assign(Request $request, $fuelOrderId)
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'carrier_id' => ['nullable', 'exists:carriers,id'],
            'transport_cost' => ['nullable', 'numeric', 'min:0'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'truck_number' => ['nullable', 'string', 'max:255'],
            'departure_time' => ['nullable', 'date'],
            'arrival_time' => ['nullable', 'date', 'after_or_equal:departure_time'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = FuelOrder::with(['transport', 'items'])->findOrFail($fuelOrderId);

        if ($order->current_step !== 'transport_assignment') {
            return back()->with('error', 'الطلب ليس في مرحلة النقل');
        }

        if ($order->transport) {
            return back()->with('error', 'تم تعيين النقل لهذا الطلب مسبقًا');
        }

        DB::transaction(function () use ($request, $order) {
            $supplier = Supplier::with('fuelPrices')->findOrFail($request->supplier_id);

            $supplierPrices = $supplier->fuelPrices->keyBy('fuel_type_id');

            $supplierTotalCost = 0;

            foreach ($order->items as $item) {
                if ($item->status !== 'approved') {
                    continue;
                }

                $price = $supplierPrices[$item->fuel_type_id]->price_per_liter ?? 0;
                $quantity = $item->approved_quantity ?? 0;

                $supplierTotalCost += ((float) $price * (float) $quantity);
            }

            if ($supplierTotalCost <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'supplier_id' => 'لا توجد أسعار مسجلة لهذا المورد أو لا توجد كميات معتمدة.',
                ]);
            }

            FuelOrderTransport::create([
                'fuel_order_id' => $order->id,
                'supplier_id' => $request->supplier_id,
                'carrier_id' => $request->carrier_id,
                'supplier_total_cost' => $supplierTotalCost,    
                'transport_cost' => $request->transport_cost,
                'driver_name' => $request->driver_name,
                'truck_number' => $request->truck_number,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'status' => 'assigned',
                'notes' => $request->notes,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);

            $oldStep = $order->current_step;

            $order->update([
                'status' => 'transport_assigned',
                'current_step' => 'finance_payment',
            ]);

            $order->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'transport_assigned',
                'from_step' => $oldStep,
                'to_step' => 'finance_payment',
                'notes' => 'تم تحديد المورد والناقل وتحويل الطلب للمالية للدفع',
            ]);
        });

        return back()->with('success', 'تم تعيين بيانات النقل بنجاح');
    }

    public function update(Request $request, $fuelOrderId)
    {
        $request->validate([
            'carrier_id' => ['nullable', 'exists:carriers,id'],
            'transport_cost' => ['nullable', 'numeric', 'min:0'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'truck_number' => ['nullable', 'string', 'max:255'],
            'departure_time' => ['nullable', 'date'],
            'arrival_time' => ['nullable', 'date', 'after_or_equal:departure_time'],
            'status' => ['required', 'in:pending,assigned,in_transit,arrived,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = FuelOrder::with(['transport', 'items'])->findOrFail($fuelOrderId);

        if (!$order->transport) {
            return back()->with('error', 'لا يوجد سجل نقل لهذا الطلب');
        }


        
        $order->transport->update([
            'carrier_id' => $request->carrier_id,
            'transport_cost' => $request->transport_cost,
            'driver_name' => $request->driver_name,
            'truck_number' => $request->truck_number,
            'departure_time' => $request->departure_time,
            'arrival_time' => $request->arrival_time,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        $order->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'transport_updated',
            'from_step' => $order->current_step,
            'to_step' => $order->current_step,
            'notes' => 'تم تحديث بيانات النقل',
        ]);

        return back()->with('success', 'تم تحديث بيانات النقل بنجاح');
    }
    
}