<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 255)]
    private string|null $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private string|null $body = null;

    #[ORM\Column(length: 255)]
    private string|null $recipient = null;

    #[ORM\Column(nullable: true)]
    private \DateTimeImmutable|null $sentAt = null;
}
