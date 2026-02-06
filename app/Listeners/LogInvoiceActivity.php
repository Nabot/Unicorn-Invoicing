<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Events\InvoiceIssued;
use App\Events\InvoiceUpdated;
use App\Events\InvoiceVoided;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;

class LogInvoiceActivity
{
    public function __construct(
        private AuditLogService $auditLogService
    ) {
    }

    /**
     * Handle invoice created event.
     */
    public function handle(InvoiceCreated|InvoiceUpdated|InvoiceIssued|InvoiceVoided $event): void
    {
        $action = match (true) {
            $event instanceof InvoiceCreated => 'invoice.created',
            $event instanceof InvoiceUpdated => 'invoice.updated',
            $event instanceof InvoiceIssued => 'invoice.issued',
            $event instanceof InvoiceVoided => 'invoice.voided',
        };

        $metadata = match (true) {
            $event instanceof InvoiceCreated => [
                'invoice_number' => $event->invoice->invoice_number,
                'client_id' => $event->invoice->client_id,
                'total' => $event->invoice->total,
            ],
            $event instanceof InvoiceUpdated => [
                'invoice_number' => $event->invoice->invoice_number,
                'status' => $event->invoice->status->value,
            ],
            $event instanceof InvoiceIssued => [
                'invoice_number' => $event->invoice->invoice_number,
                'issue_date' => $event->invoice->issue_date?->toDateString(),
            ],
            $event instanceof InvoiceVoided => [
                'invoice_number' => $event->invoice->invoice_number,
            ],
        };

        $this->auditLogService->log(
            Auth::id(),
            $action,
            $event->invoice,
            $metadata
        );
    }
}
