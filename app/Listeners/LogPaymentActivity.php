<?php

namespace App\Listeners;

use App\Events\PaymentDeleted;
use App\Events\PaymentRecorded;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;

class LogPaymentActivity
{
    public function __construct(
        private AuditLogService $auditLogService
    ) {
    }

    /**
     * Handle payment events.
     */
    public function handle(PaymentRecorded|PaymentDeleted $event): void
    {
        $action = match (true) {
            $event instanceof PaymentRecorded => 'payment.recorded',
            $event instanceof PaymentDeleted => 'payment.deleted',
        };

        $metadata = match (true) {
            $event instanceof PaymentRecorded => [
                'invoice_id' => $event->invoice->id,
                'invoice_number' => $event->invoice->invoice_number,
                'amount' => $event->payment->amount,
                'method' => $event->payment->method->value,
            ],
            $event instanceof PaymentDeleted => [
                'invoice_id' => $event->invoice->id,
                'invoice_number' => $event->invoice->invoice_number,
                'amount' => $event->payment->amount,
            ],
        };

        $this->auditLogService->log(
            Auth::id(),
            $action,
            $event->payment,
            $metadata
        );
    }
}
