<?php

namespace App\Enums;

enum UserRole: string
{
    case Owner = 'owner';
    case BranchStaff = 'branch_staff';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Pemilik',
            self::BranchStaff => 'Staf Cabang',
        };
    }
}
