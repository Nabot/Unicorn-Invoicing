<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('record-payments') || $user->can('view-all-invoices');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payment $payment): bool
    {
        $invoice = $payment->invoice;

        // Check company scope
        if ($user->company_id !== $invoice->company_id) {
            return false;
        }

        // Admin can view all payments
        if ($user->can('view-all-invoices')) {
            return true;
        }

        // Staff can view payments for invoices they created
        return $user->can('record-payments') && $invoice->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('record-payments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payment $payment): bool
    {
        $invoice = $payment->invoice;

        // Check company scope
        if ($user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->can('record-payments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payment $payment): bool
    {
        $invoice = $payment->invoice;

        // Check company scope
        if ($user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->can('record-payments');
    }
}
