<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-all-invoices') || $user->can('view-assigned-invoices');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        // Check company scope
        if ($user->company_id !== $invoice->company_id) {
            return false;
        }

        // Admin can view all invoices
        if ($user->can('view-all-invoices')) {
            return true;
        }

        // Staff and Agent can view assigned invoices
        return $user->can('view-assigned-invoices') && $invoice->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-invoices');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        // Check company scope
        if ($user->company_id !== $invoice->company_id) {
            return false;
        }

        if (! $invoice->canBeEdited()) {
            return false;
        }

        return $user->can('edit-invoices');
    }

    /**
     * Determine whether the user can issue the invoice.
     */
    public function issue(User $user, Invoice $invoice): bool
    {
        // Check company scope
        if ($user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->can('issue-invoices');
    }

    /**
     * Determine whether the user can void the invoice.
     */
    public function void(User $user, Invoice $invoice): bool
    {
        // Check company scope
        if ($user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->can('void-invoices');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        // Check company scope
        if ($user->company_id !== $invoice->company_id) {
            return false;
        }

        // Prevent deletion if invoice has payments
        if ($invoice->payments()->exists()) {
            return false;
        }

        return $user->can('delete-invoices');
    }
}
