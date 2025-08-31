<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Notification;
use App\Factory\NotificationFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class NotificationTest extends ApiTestCase
{
    use ResetDatabase, Factories;

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
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(30, $response->toArray()['member']);  // Default pagination is 30 items per page
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
                'last' => '/api/notifications?page=4',
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
            ]
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
        ]);
    }

    public function testUpdateNotification(): void
    {
        $notification = NotificationFactory::createOne([
            'recipient' => 'beyonce.knowles@gmail.com',
            'subject' => 'Initial subject',
            'body' => 'Hey so this is a message'
        ]);
        $iri = $this->findIriBy(Notification::class, ['recipient' => 'beyonce.knowles@gmail.com']);

        $client = self::$client;
        $response = $client->request('PATCH', '/api/notifications/' . $notification->getId()->toString(), [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => [
                'subject' => 'Updated subject',
                'body' => 'Actually, I changed my mind',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@id' => $iri,
            'subject' => 'Updated subject',
            'body' => 'Actually, I changed my mind',
            'recipient' => 'beyonce.knowles@gmail.com'
        ]);
    }

    public function testDeleteNotificationIsNotAllowed(): void
    {
        $client = self::$client;
        $response = $client->request('DELETE', '/api/notifications', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [],
        ]);
        // DELETE route is forbidden on this API resource
        $this->assertResponseStatusCodeSame(405);
    }

}
