<?php

namespace App\Http\Controllers;

use App\Models\FuelOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FuelOrderFinanceController extends Controller
{
    public function approveFinance(Request $request, $id)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $order = FuelOrder::with('finance')->findOrFail($id);

        if ($order->current_step !== 'finance_review') {
            return back()->with('error', 'الطلب ليس في مرحلة مراجعة توفر المبلغ');
        }

        DB::transaction(function () use ($request, $order) {
            $oldStep = $order->current_step;

            $finance = $order->finance()->updateOrCreate(
                ['fuel_order_id' => $order->id],
                [
                    'status' => 'approved',
                    'amount' => $request->amount,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]
            );

            $order->update([
                'status' => 'finance_approved',
                'current_step' => 'transport_assignment',
            ]);

            $order->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'finance_approved',
                'from_step' => $oldStep,
                'to_step' => 'transport_assignment',
                'notes' => 'تم تأكيد توفر المبلغ بمبلغ: ' . number_format((float) $request->amount, 2) . ' وتحويل الطلب للنقل',
            ]);
        });

        return redirect()
            ->route('fuel-orders.show', $order->id)
            ->with('success', 'تم تأكيد توفر المبلغ وتحويل الطلب للنقل');
    }

    public function rejectFinance(Request $request, $id)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $order = FuelOrder::with('finance')->findOrFail($id);

        if ($order->current_step !== 'finance_review') {
            return back()->with('error', 'الطلب ليس في مرحلة مراجعة توفر المبلغ');
        }

        DB::transaction(function () use ($request, $order) {
            $oldStep = $order->current_step;

            $order->finance()->updateOrCreate(
                ['fuel_order_id' => $order->id],
                [
                    'status' => 'rejected',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]
            );

            $order->update([
                'status' => 'returned_to_operations',
                'current_step' => 'operations_review',
            ]);

            $order->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'finance_rejected',
                'from_step' => $oldStep,
                'to_step' => 'operations_review',
                'notes' => 'تم إرجاع الطلب للتشغيل. السبب: ' . $request->reason,
            ]);
        });

        return redirect()
            ->route('fuel-orders.show', $order->id)
            ->with('success', 'تم إرجاع الطلب للتشغيل');
    }

    public function confirmPayment(Request $request, $id)
    {

        $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'payment_reference' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fuel_order_finances', 'payment_reference')
                    ->where(fn ($query) => $query->where('bank_name', $request->bank_name)),
            ],
        ]);

        $order = FuelOrder::with('finance')->findOrFail($id);

        if ($order->current_step !== 'finance_payment') {
            return back()->with('error', 'الطلب ليس في مرحلة الدفع');
        }

        DB::transaction(function () use ($request, $order) {
            $oldStep = $order->current_step;

            $order->finance()->updateOrCreate(
                ['fuel_order_id' => $order->id],
                [
                    'status' => 'paid',
                    'payment_reference' => $request->payment_reference,
                    'paid_at' => now(),
                ]
            );

            $order->update([
                'status' => 'payment_confirmed',
                'current_step' => 'station_receipt',
            ]);

            $order->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'payment_confirmed',
                'from_step' => $oldStep,
                'to_step' => 'station_receipt',
                'notes' => 'تم تسجيل الدفع. رقم المرجع: ' . $request->payment_reference . ' وتحويل الطلب لمرحلة الاستلام',
            ]);
        });

        return redirect()
            ->route('fuel-orders.show', $order->id)
            ->with('success', 'تم تسجيل الدفع وتحويل الطلب لمرحلة الاستلام');
    }
}
