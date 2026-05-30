<?php

declare(strict_types=1);


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


final class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request(
            method: 'GET',
            uri: '/home'
        );

        self::assertResponseIsSuccessful();
    }
}
