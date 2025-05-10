<?php

namespace App\Enums;

enum AdStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case INACTIVE = 'inactive';
    case SOLD = 'sold';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::PENDING => 'Pending',
            self::INACTIVE => 'Inactive',
            self::SOLD => 'Sold',
            self::EXPIRED => 'Expired',
        };
    }

}
