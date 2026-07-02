<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Single-instance authorization for a route-model-bound order — closes the
     * gap the list-level scope can't (a branch staffer hitting another branch's
     * order URL directly).
     */
    public function view(User $user, Order $order): bool
    {
        return $user->role === UserRole::Owner || $order->branch_id === $user->branch_id;
    }

    public function update(User $user, Order $order): bool
    {
        return $this->view($user, $order);
    }
}
