<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage-clients') || $user->can('view-assigned-clients');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        // Check company scope
        if ($user->company_id !== $client->company_id) {
            return false;
        }

        // Admin and Staff can view all clients in their company
        if ($user->can('manage-clients')) {
            return true;
        }

        // Agent can only view assigned clients
        return $user->can('view-assigned-clients') && $client->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage-clients');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        // Check company scope
        if ($user->company_id !== $client->company_id) {
            return false;
        }

        return $user->can('manage-clients');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        // Check company scope
        if ($user->company_id !== $client->company_id) {
            return false;
        }

        return $user->can('manage-clients');
    }
}
