<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display reports index.
     */
    public function index(): View
    {
        return view('reports.index');
    }

    /**
     * Display sales summary report.
     */
    public function salesSummary(Request $request): View
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $invoices = Invoice::forCompany($request->user()->company_id)
            ->whereBetween('issue_date', [$fromDate, $toDate])
            ->where('status', '!=', InvoiceStatus::VOID)
            ->get();

        $summary = [
            'total_invoices' => $invoices->count(),
            'subtotal' => $invoices->sum('subtotal'),
            'vat_total' => $invoices->sum('vat_total'),
            'total' => $invoices->sum('total'),
            'amount_paid' => $invoices->sum('amount_paid'),
            'balance_due' => $invoices->sum('balance_due'),
        ];

        // Group by status
        $byStatus = $invoices->groupBy('status')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ];
        });

        // Invoice aging
        $aging = [
            '0-30' => $invoices->filter(fn ($inv) => $inv->due_date->diffInDays(now()) <= 30 && $inv->balance_due > 0)->sum('balance_due'),
            '31-60' => $invoices->filter(fn ($inv) => $inv->due_date->diffInDays(now()) > 30 && $inv->due_date->diffInDays(now()) <= 60 && $inv->balance_due > 0)->sum('balance_due'),
            '61-90' => $invoices->filter(fn ($inv) => $inv->due_date->diffInDays(now()) > 60 && $inv->due_date->diffInDays(now()) <= 90 && $inv->balance_due > 0)->sum('balance_due'),
            '90+' => $invoices->filter(fn ($inv) => $inv->due_date->diffInDays(now()) > 90 && $inv->balance_due > 0)->sum('balance_due'),
        ];

        return view('reports.sales-summary', compact('summary', 'byStatus', 'aging', 'fromDate', 'toDate'));
    }
}
