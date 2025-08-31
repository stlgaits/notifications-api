<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\SendNotificationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Workflow\WorkflowInterface;

readonly class NotificationStateProcessor implements ProcessorInterface
{
    public function __construct(
        private WorkflowInterface $sendingNotificationStateMachine,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private SendNotificationInterface $notificationSender,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$this->sendingNotificationStateMachine->can($data, 'send')) {
            return $data;
        }

        $this->sendingNotificationStateMachine->apply($data, 'send');

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        $this->notificationSender->send($result);

        return $result;
    }
}
