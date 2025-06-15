<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CartControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = self::createClient();

        $hash = md5(uniqid('', true));
        $client->request('GET', '/api/cart/' . $hash);

        self::assertEquals(422, $client->getResponse()->getStatusCode());
        self::assertResponseIsSuccessful();
    }
}
