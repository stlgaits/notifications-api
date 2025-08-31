<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource]
#[GetCollection(
    name: 'list_notifications',
)]
#[Post(
    name: 'create_notification',
)]
#[Patch(
    name: 'send_notification',  // so far it's a full-on patch but we need to change that to a custom operation later
)]
class Notification
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private AbstractUid|null $id = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    #[ORM\Column(length: 255)]
    private string|null $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private string|null $body = null;

    #[ORM\Column(length: 255)]
    private string|null $recipient = null;

    #[ORM\Column(nullable: true)]
    private \DateTimeImmutable|null $sentAt = null;

    public function getId(): ?AbstractUid
    {
        return $this->id;
    }

    public function setId(AbstractUid $id): void
    {
        $this->id = $id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): void
    {
        $this->sentAt = $sentAt;
    }
}
