<?php

declare(strict_types=1);

namespace App\Notifier;

use App\Entity\Notification;

interface SendNotificationInterface
{
    public function send(Notification $notification): void;
}
