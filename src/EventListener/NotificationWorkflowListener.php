<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Notification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Event\TransitionEvent;

class NotificationWorkflowListener
{
    #[AsEventListener(event: 'workflow.sending_notification.transition.send')]
    public function onWorkflowSendingNotificationTransitionSend(TransitionEvent $event): void
    {
        $notification = $event->getSubject();

        if (!$notification instanceof Notification) {
            return;
        }

        if (null === $notification->getSentAt()) {
            $notification->setSentAt(new \DateTimeImmutable());
        }
    }
}
