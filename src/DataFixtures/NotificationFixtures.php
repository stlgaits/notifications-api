<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Story\DefaultNotificationsStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NotificationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultNotificationsStory::load();
    }
}
