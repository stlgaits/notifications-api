<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\NotificationFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class DefaultNotificationsStory extends Story
{
    public function build(): void
    {
        NotificationFactory::createMany(100);
    }
}
