<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\NotificationStateEnum;
use App\Repository\NotificationRepository;
use App\State\NotificationStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource(
    paginationItemsPerPage: 10
)]
#[GetCollection(
    name: 'list_notifications',
)]
#[Post(
    name: 'create_notification',
)]
#[Patch(
    uriTemplate: '/notifications/{id}/send',
    input: false,
    output: Notification::class,
    name: 'send_notification',
    processor: NotificationStateProcessor::class
)]
class Notification
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[Assert\Uuid]
    private AbstractUid|null $id = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string|null $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string|null $body = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string|null $recipient = null;

    #[ORM\Column(nullable: true)]
    private \DateTimeImmutable|null $sentAt = null;

    #[ORM\Column(enumType: NotificationStateEnum::class)]
    #[Assert\NotBlank]
    private NotificationStateEnum|null $state = null;

    public function getId(): AbstractUid|null
    {
        return $this->id;
    }

    public function setId(AbstractUid $id): void
    {
        $this->id = $id;
    }

    public function getSubject(): string|null
    {
        return $this->subject;
    }

    public function setSubject(string|null $subject): void
    {
        $this->subject = $subject;
    }

    public function getBody(): string|null
    {
        return $this->body;
    }

    public function setBody(string|null $body): void
    {
        $this->body = $body;
    }

    public function getRecipient(): string|null
    {
        return $this->recipient;
    }

    public function setRecipient(string|null $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getSentAt(): \DateTimeImmutable|null
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable|null $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    public function getStateValue(): string|null
    {
        return $this->state?->value;
    }

    public function setStateValue(string $stateValue): void
    {
        $enum = NotificationStateEnum::tryFrom($stateValue);
        if (null === $enum) {
            throw new \InvalidArgumentException('Invalid state value: ' . $stateValue);
        }
        $this->state = $enum;
    }

    //    public function getState(): NotificationStateEnum|null
    //    {
    //        return $this->state;
    //    }
    //
    //    public function setState(NotificationStateEnum $state): static
    //    {
    //        $this->state = $state;
    //
    //        return $this;
    //    }
}
