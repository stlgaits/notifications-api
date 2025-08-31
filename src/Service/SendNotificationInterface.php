<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Notification;

interface SendNotificationInterface
{
    public function send(Notification $notification): void;
}
