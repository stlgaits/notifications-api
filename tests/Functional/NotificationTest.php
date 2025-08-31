<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Notification;
use App\Enum\NotificationStateEnum;
use App\Factory\NotificationFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class NotificationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    private static Client $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = static::createClient();
    }

    public function testReadNotificationsList(): void
    {
        NotificationFactory::createMany(100);

        $client = self::$client;
        $response = $client->request('GET', '/api/notifications', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(10, $response->toArray()['member']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Notification',
            '@id' => '/api/notifications',
            '@type' => 'Collection',
            'totalItems' => 100,
            'view' => [
                '@id' => '/api/notifications?page=1',
                '@type' => 'PartialCollectionView',
                'first' => '/api/notifications?page=1',
                'last' => '/api/notifications?page=10',
                'next' => '/api/notifications?page=2',
            ],
        ]);
    }

    public function testCreateNotification(): void
    {
        $client = self::$client;
        $response = $client->request('POST', '/api/notifications', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'recipient' => 'beyonce.knowles@gmail.com',
                'subject' => 'Test email',
                'body' => 'Email body content',
                'stateValue' => NotificationStateEnum::PENDING->value,
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Notification',
            '@type' => 'Notification',
            'subject' => 'Test email',
            'body' => 'Email body content',
            'recipient' => 'beyonce.knowles@gmail.com',
            'stateValue' => NotificationStateEnum::PENDING->value,
        ]);
    }

    public function testCannotCreateInvalidNotification(): void
    {
        $client = self::$client;
        $response = $client->request('POST', '/api/notifications', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'recipient' => 'test wrong email',
                'subject' => 'Test email',
                'body' => 'Email body content',
                'stateValue' => NotificationStateEnum::PENDING->value,
            ],
        ]);
        $this->assertResponseIsUnprocessable();
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolation',
            '@type' => 'ConstraintViolation',
            'status' => 422,
            'description' => 'recipient: This value is not a valid email address.',
            'detail' => 'recipient: This value is not a valid email address.',
        ]);
    }

    public function testCannotUpdateNotifications(): void
    {
        $notification = NotificationFactory::createOne([
            'recipient' => 'beyonce.knowles@gmail.com',
            'subject' => 'Initial subject',
            'body' => 'Hey so this is a message',
            'stateValue' => NotificationStateEnum::PENDING->value,
        ]);
        $iri = $this->findIriBy(Notification::class, [
            'recipient' => 'beyonce.knowles@gmail.com',
        ]);

        $client = self::$client;
        $response = $client->request('PATCH', '/api/notifications/' . $notification->getId()->toString(), [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => [
                'subject' => 'Updated subject',
                'body' => 'Actually, I changed my mind',
            ],
        ]);

        // PATCH route is forbidden on this API resource
        $this->assertResponseStatusCodeSame(405);
    }

    public function testDeleteNotificationIsNotAllowed(): void
    {
        $client = self::$client;
        $response = $client->request('DELETE', '/api/notifications', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [],
        ]);
        // DELETE route is forbidden on this API resource
        $this->assertResponseStatusCodeSame(405);
    }

    public function testSendNotificationSuccessfully(): void
    {
        $notification = NotificationFactory::createOne([
            'recipient' => 'test@example.com',
            'subject' => 'Test notification',
            'body' => 'This is a test notification waiting to be sent!',
            'stateValue' => NotificationStateEnum::PENDING->value,
            'sentAt' => null,
        ]);

        $client = self::$client;
        $response = $client->request('PATCH', '/api/notifications/' . $notification->getId()->toString() . '/send');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Notification',
            '@type' => 'Notification',
            'subject' => 'Test notification',
            'body' => 'This is a test notification waiting to be sent!',
            'recipient' => 'test@example.com',
            'stateValue' => NotificationStateEnum::SENT->value,
        ]);
    }

    public function testSendNotificationIsIdempotent(): void
    {
        $notification = NotificationFactory::createOne([
            'recipient' => 'test@example.com',
            'subject' => 'Already sent notification',
            'body' => 'This was already sent',
            'stateValue' => NotificationStateEnum::SENT->value,
            'sentAt' => new \DateTimeImmutable('2025-08-30 10:00:00'),
        ]);

        $client = self::$client;
        $response = $client->request('PATCH', '/api/notifications/' . $notification->getId()->toString() . '/send');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'subject' => 'Already sent notification',
            'body' => 'This was already sent',
            'recipient' => 'test@example.com',
            'stateValue' => NotificationStateEnum::SENT->value,
            'sentAt' => '2025-08-30T10:00:00+00:00', // sentAt should remain the original time, not be updated
        ]);
    }

    public function testSendNotificationWithInvalidId(): void
    {
        $client = self::$client;
        $invalidId = '550e8400-e29b-41d4-a716-446655440000'; // Valid UUID format but non-existent

        $response = $client->request('PATCH', '/api/notifications/' . $invalidId . '/send');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSendNotificationDoesNotAcceptBodyInput(): void
    {
        $notification = NotificationFactory::createOne([
            'recipient' => 'test@example.com',
            'subject' => 'Test notification',
            'body' => 'This is a test',
            'stateValue' => NotificationStateEnum::PENDING->value,
            'sentAt' => null,
        ]);

        $client = self::$client;

        // body input should be ignored
        $response = $client->request('PATCH', '/api/notifications/' . $notification->getId()->toString() . '/send', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'subject' => 'Trying to change subject',
                'maliciousField' => 'should be ignored',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'subject' => 'Test notification',  // The subject should NOT have changed
            'body' => 'This is a test',
            'recipient' => 'test@example.com',
            'stateValue' => NotificationStateEnum::SENT->value,
        ]);
    }
}
