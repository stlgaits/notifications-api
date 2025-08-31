<?php

declare(strict_types=1);

namespace App\Exception;

class NotificationNotFoundException extends \RuntimeException
{
    public function __construct(string $id, int $code = 0, \Throwable|null $previous = null)
    {
        parent::__construct(sprintf('Notification with id "%s" not found.', $id), $code, $previous);
    }
}
