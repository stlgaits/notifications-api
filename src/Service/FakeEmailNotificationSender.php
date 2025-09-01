<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Notification;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class FakeEmailNotificationSender implements SendNotificationInterface
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.notification')]
        private LoggerInterface $logger
    ) {
    }

    public function send(Notification $notification): void
    {
        $this->logger->info('Sent fake email notification to someone', [
            'id' => $notification->getId()?->toString(),
            'to' => $notification->getRecipient(),
            'subject' => $notification->getSubject(),
            'body' => $notification->getBody(),
            'sentAt' => $notification->getSentAt()?->format('Y-m-d H:i:s'),
            'state' => $notification->getStateValue(),
        ]);
    }
}
