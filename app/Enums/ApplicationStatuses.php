<?php

namespace App\Enums;

enum ApplicationStatuses: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
}