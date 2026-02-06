<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Invoice $invoice): View
    {
        $this->authorize('create', Payment::class);

        return view('payments.create', compact('invoice'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        try {
            $payment = $this->paymentService->recordPayment(
                $invoice,
                $request->user()->id,
                $request->validated()
            );

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Invoice $invoice, Payment $payment): View
    {
        $this->authorize('update', $payment);

        return view('payments.edit', compact('invoice', 'payment'));
    }

    /**
     * Update the specified payment.
     */
    public function update(UpdatePaymentRequest $request, Invoice $invoice, Payment $payment): RedirectResponse
    {
        try {
            $this->paymentService->updatePayment($payment, $request->validated());

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Invoice $invoice, Payment $payment): RedirectResponse
    {
        $this->authorize('delete', $payment);

        try {
            $this->paymentService->deletePayment($payment);

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
