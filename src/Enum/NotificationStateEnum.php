<?php

declare(strict_types=1);

namespace App\Enum;

enum NotificationStateEnum: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
}
