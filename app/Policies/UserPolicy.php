<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Admin Access
    public function admin(User $user)
    {
        return $user->role === 'admin';
    }

    // Owner Access
    public function owner(User $user)
    {
        return $user->role === 'owner';
    }

    // Renter Access
    public function renter(User $user)
    {
        return $user->role === 'renter';
    }
}
