<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Notification;
use App\Enum\NotificationStateEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Notification>
 */
final class NotificationFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Notification::class;
    }

    protected function defaults(): array
    {
        return [
            'body' => self::faker()->text(),
            'recipient' => self::faker()->email(),
            'subject' => self::faker()->sentence(4),
            'sentAt' => self::faker()->dateTimeThisMonth(),
            'stateValue' => NotificationStateEnum::PENDING->value,
        ];
    }
}
