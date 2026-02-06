<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): View
    {
        $companyId = $request->user()->company_id;

        // Cache dashboard statistics
        $stats = CacheService::getInvoiceStats($companyId, function () use ($companyId) {
            return [
                'totalInvoices' => Invoice::forCompany($companyId)->count(),
                'totalClients' => Client::forCompany($companyId)->count(),
                'outstandingBalance' => Invoice::forCompany($companyId)
                    ->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID])
                    ->sum('balance_due'),
                'overdueInvoices' => Invoice::forCompany($companyId)
                    ->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID])
                    ->where('due_date', '<', now())
                    ->count(),
                'monthlyRevenue' => Invoice::forCompany($companyId)
                    ->where('status', InvoiceStatus::PAID)
                    ->whereYear('issue_date', now()->year)
                    ->whereMonth('issue_date', now()->month)
                    ->sum('total'),
                'pendingPayments' => Invoice::forCompany($companyId)
                    ->where('status', InvoiceStatus::PARTIALLY_PAID)
                    ->sum('balance_due'),
            ];
        });

        extract($stats);
        
        // Recent invoices
        $recentInvoices = Invoice::forCompany($companyId)
            ->with(['client'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Recent activity (audit logs) - get logs for invoices/clients/payments in this company
        $recentActivity = AuditLog::where(function ($query) use ($companyId) {
            $query->whereHasMorph('entity', [Invoice::class, Client::class], function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->orWhereHas('actor', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            });
        })
            ->with(['actor'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalInvoices',
            'totalClients',
            'outstandingBalance',
            'overdueInvoices',
            'monthlyRevenue',
            'pendingPayments',
            'recentInvoices',
            'recentActivity'
        ));
    }
}
