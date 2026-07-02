<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Menunggu Pembayaran',
            self::Paid => 'Dibayar',
            self::Processing => 'Diproses',
            self::Shipped => 'Dikirim',
            self::Completed => 'Selesai',
            self::Expired => 'Kedaluwarsa',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Tailwind badge classes for admin/public status display.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PendingPayment => 'bg-amber-100 text-amber-700',
            self::Paid => 'bg-green-100 text-green-700',
            self::Processing => 'bg-blue-100 text-blue-700',
            self::Shipped => 'bg-indigo-100 text-indigo-700',
            self::Completed => 'bg-emerald-100 text-emerald-700',
            self::Expired => 'bg-gray-100 text-gray-600',
            self::Cancelled => 'bg-red-100 text-red-700',
        };
    }
}
