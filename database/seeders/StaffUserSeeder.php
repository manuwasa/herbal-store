<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    /**
     * One demo Staf Cabang account per non-default branch, so the role-scoped
     * admin screens (Pesanan/Laporan/Transfer Stok) have someone to log in as
     * and actually demonstrate the branch-scoped view, not just the Owner's.
     */
    public function run(): void
    {
        $demoStaff = [
            'staf.jakarta@herbalstore.test' => ['name' => 'Staf Cabang Jakarta', 'branchCode' => 'JKT-01'],
            'staf.surabaya@herbalstore.test' => ['name' => 'Staf Cabang Surabaya', 'branchCode' => 'SBY-01'],
        ];

        foreach ($demoStaff as $email => $info) {
            $branch = Branch::query()->where('code', $info['branchCode'])->first();

            User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => $info['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::BranchStaff->value,
                    'branch_id' => $branch?->id,
                ]
            );
        }
    }
}
