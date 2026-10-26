<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    /**
     * Determine whether the user can view the sale.
     */
    public function view(User $user, Sale $sale): bool
    {
        if ($user->isOwner() || $user->isStaff()) {
            return true;
        }

        return $sale->user_id === $user->id;
    }

    /**
     * Determine whether the user can cancel the sale.
     * Strictly restricted to Owner only!
     */
    public function cancel(User $user, Sale $sale): bool
    {
        return $user->isOwner() && $sale->canBeCancelled();
    }
}
