<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Events\PaymentDeleted;
use App\Events\PaymentRecorded;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private InvoiceCalculatorService $calculatorService
    ) {
    }

    /**
     * Record a payment for an invoice.
     *
     * @param  array<string, mixed>  $paymentData
     */
    public function recordPayment(Invoice $invoice, int $userId, array $paymentData): Payment
    {
        if ($invoice->status === InvoiceStatus::VOID) {
            throw new \Exception('Cannot record payment for void invoice.');
        }

        if ($invoice->status === InvoiceStatus::PAID) {
            throw new \Exception('Invoice is already fully paid.');
        }

        return DB::transaction(function () use ($invoice, $userId, $paymentData) {
            $amount = (float) $paymentData['amount'];
            $newAmountPaid = $invoice->amount_paid + $amount;
            $balanceDue = $this->calculatorService->calculateBalanceDue($invoice->total, $newAmountPaid);

            // Prevent overpayment
            if ($balanceDue < 0) {
                throw new \Exception('Payment amount exceeds invoice balance.');
            }

            // Create payment
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_date' => $paymentData['payment_date'],
                'method' => $paymentData['method'],
                'reference' => $paymentData['reference'] ?? null,
                'created_by' => $userId,
            ]);

            // Update invoice
            $status = $balanceDue <= 0 ? InvoiceStatus::PAID : InvoiceStatus::PARTIALLY_PAID;
            if ($invoice->status === InvoiceStatus::DRAFT) {
                $status = InvoiceStatus::PARTIALLY_PAID;
            }

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'balance_due' => $balanceDue,
                'status' => $status,
            ]);

            event(new PaymentRecorded($payment, $invoice));

            return $payment->load('invoice');
        });
    }

    /**
     * Update a payment.
     *
     * @param  array<string, mixed>  $paymentData
     */
    public function updatePayment(Payment $payment, array $paymentData): Payment
    {
        $invoice = $payment->invoice;

        if ($invoice->status === InvoiceStatus::VOID) {
            throw new \Exception('Cannot update payment for void invoice.');
        }

        return DB::transaction(function () use ($payment, $invoice, $paymentData) {
            $oldAmount = $payment->amount;
            $newAmount = (float) $paymentData['amount'];

            // Recalculate invoice totals
            $newAmountPaid = $invoice->amount_paid - $oldAmount + $newAmount;
            $balanceDue = $this->calculatorService->calculateBalanceDue($invoice->total, $newAmountPaid);

            // Prevent overpayment
            if ($balanceDue < 0) {
                throw new \Exception('Payment amount exceeds invoice balance.');
            }

            // Update payment
            $payment->update([
                'amount' => $newAmount,
                'payment_date' => $paymentData['payment_date'] ?? $payment->payment_date,
                'method' => $paymentData['method'] ?? $payment->method,
                'reference' => $paymentData['reference'] ?? $payment->reference,
            ]);

            // Update invoice
            $status = $balanceDue <= 0 ? InvoiceStatus::PAID : InvoiceStatus::PARTIALLY_PAID;
            if ($invoice->status === InvoiceStatus::DRAFT) {
                $status = InvoiceStatus::PARTIALLY_PAID;
            }

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'balance_due' => $balanceDue,
                'status' => $status,
            ]);

            return $payment->load('invoice');
        });
    }

    /**
     * Delete a payment.
     */
    public function deletePayment(Payment $payment): void
    {
        $invoice = $payment->invoice;

        if ($invoice->status === InvoiceStatus::VOID) {
            throw new \Exception('Cannot delete payment for void invoice.');
        }

        DB::transaction(function () use ($payment, $invoice) {
            $amount = $payment->amount;

            // Recalculate invoice totals
            $newAmountPaid = max(0, $invoice->amount_paid - $amount);
            $balanceDue = $this->calculatorService->calculateBalanceDue($invoice->total, $newAmountPaid);

            // Delete payment
            $payment->delete();

            // Update invoice status
            $status = $newAmountPaid <= 0
                ? ($invoice->status === InvoiceStatus::ISSUED ? InvoiceStatus::ISSUED : InvoiceStatus::DRAFT)
                : InvoiceStatus::PARTIALLY_PAID;

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'balance_due' => $balanceDue,
                'status' => $status,
            ]);

            event(new PaymentDeleted($payment, $invoice));
        });
    }
}
