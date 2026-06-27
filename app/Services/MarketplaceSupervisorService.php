<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class MarketplaceSupervisorService
{
    public function assignSupervisor(Model $model): ?User
    {
        if (! Schema::hasColumn($model->getTable(), 'supervisor_user_id')) {
            return null;
        }

        if ($model->getAttribute('supervisor_user_id')) {
            return User::query()->find($model->getAttribute('supervisor_user_id'));
        }

        $admin = $this->nextAdmin();

        if (! $admin) {
            return null;
        }

        $model->forceFill([
            'supervisor_user_id' => $admin->id,
            'supervisor_assigned_at' => now(),
        ])->save();

        return $admin;
    }

    private function nextAdmin(): ?User
    {
        $query = User::query();

        if (Schema::hasColumn('users', 'is_admin')) {
            return $query->where('is_admin', true)->inRandomOrder()->first();
        }

        if (Schema::hasColumn('users', 'account_type')) {
            return $query->where('account_type', 'admin')->inRandomOrder()->first();
        }

        if (Schema::hasColumn('users', 'role')) {
            return $query->where('role', 'admin')->inRandomOrder()->first();
        }

        return null;
    }
}