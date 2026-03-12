<?php

namespace App\Enum;

enum ItemStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}