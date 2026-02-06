<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Index for filtering by status
            if (!$this->hasIndex('invoices', 'invoices_status_index')) {
                $table->index('status', 'invoices_status_index');
            }
            // Index for filtering by due_date
            if (!$this->hasIndex('invoices', 'invoices_due_date_index')) {
                $table->index('due_date', 'invoices_due_date_index');
            }
            // Index for filtering by issue_date
            if (!$this->hasIndex('invoices', 'invoices_issue_date_index')) {
                $table->index('issue_date', 'invoices_issue_date_index');
            }
            // Composite index for company_id and status (common query pattern)
            if (!$this->hasIndex('invoices', 'invoices_company_status_index')) {
                $table->index(['company_id', 'status'], 'invoices_company_status_index');
            }
            // Composite index for company_id and due_date
            if (!$this->hasIndex('invoices', 'invoices_company_due_date_index')) {
                $table->index(['company_id', 'due_date'], 'invoices_company_due_date_index');
            }
        });

        Schema::table('clients', function (Blueprint $table) {
            // Index for searching by name
            if (!$this->hasIndex('clients', 'clients_name_index')) {
                $table->index('name', 'clients_name_index');
            }
            // Index for searching by email
            if (!$this->hasIndex('clients', 'clients_email_index')) {
                $table->index('email', 'clients_email_index');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            // Index for filtering by payment_date
            if (!$this->hasIndex('payments', 'payments_payment_date_index')) {
                $table->index('payment_date', 'payments_payment_date_index');
            }
            // Composite index for invoice_id and payment_date
            if (!$this->hasIndex('payments', 'payments_invoice_date_index')) {
                $table->index(['invoice_id', 'payment_date'], 'payments_invoice_date_index');
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            // Index for entity lookups
            if (!$this->hasIndex('audit_logs', 'audit_logs_entity_index')) {
                $table->index(['entity_type', 'entity_id'], 'audit_logs_entity_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_status_index');
            $table->dropIndex('invoices_due_date_index');
            $table->dropIndex('invoices_issue_date_index');
            $table->dropIndex('invoices_company_status_index');
            $table->dropIndex('invoices_company_due_date_index');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_name_index');
            $table->dropIndex('clients_email_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_payment_date_index');
            $table->dropIndex('payments_invoice_date_index');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_logs_entity_index');
        });
    }

    /**
     * Check if an index exists.
     */
    private function hasIndex(string $table, string $index): bool
    {
        try {
            $connection = Schema::getConnection();
            $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
            $doctrineTable = $doctrineSchemaManager->listTableDetails($table);
            return $doctrineTable->hasIndex($index);
        } catch (\Exception $e) {
            return false;
        }
    }
};
