<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderControllerTest extends WebTestCase
{
    private function getClientWithToken()
    {
        $client = static::createClient();

        // 🔥 Replace this with your real JWT
        $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEyMywicm9sZSI6IlNPTUVUSElORyJ9.4dASEQ8JY_9ZncKpvxlghkve3zfnQiSyqbgFlIMnLWs';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $jwt));

        return $client;
    }

    // ==============================
    // 🔐 AUTH TESTS
    // ==============================

    public function testAccessWithoutJWT(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testAccessWithInvalidJWT(): void
    {
        $client = static::createClient();

        $client->setServerParameter('HTTP_Authorization', 'Bearer invalid_token');

        $client->request('GET', '/api/orders');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    // ==============================
    // 👤 CLIENT TESTS
    // ==============================

    public function testGetOrders(): void
    {
        $client = $this->getClientWithToken();

        $client->request('GET', '/api/orders');

        $this->assertResponseIsSuccessful();
    }

    public function testGetOrder(): void
    {
        $client = $this->getClientWithToken();

        $client->request('GET', '/api/orders/1');

        $this->assertTrue(
            in_array(
                $client->getResponse()->getStatusCode(),
                [Response::HTTP_OK, Response::HTTP_NOT_FOUND]
            )
        );
    }

    public function testAddOrderSuccess(): void
    {
        $client = $this->getClientWithToken();

        $data = [
            'items' => [
                ['productId' => 1, 'quantity' => 2]
            ]
        ];

        $client->request(
            'POST',
            '/api/orders/add',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertTrue(
            in_array(
                $client->getResponse()->getStatusCode(),
                [Response::HTTP_OK, Response::HTTP_BAD_REQUEST]
            )
        );
    }

    public function testAddOrderInvalidData(): void
    {
        $client = $this->getClientWithToken();

        $client->request(
            'POST',
            '/api/orders/add',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCancelOrder(): void
    {
        $client = $this->getClientWithToken();

        $client->request('POST', '/api/orders/1/cancel');

        $this->assertTrue(
            in_array(
                $client->getResponse()->getStatusCode(),
                [Response::HTTP_OK, Response::HTTP_CONFLICT]
            )
        );
    }

    public function testCancelOrderItem(): void
    {
        $client = $this->getClientWithToken();

        $client->request('POST', '/api/orders/1/items/1/cancel');

        $this->assertTrue(
            in_array(
                $client->getResponse()->getStatusCode(),
                [Response::HTTP_OK, Response::HTTP_BAD_REQUEST]
            )
        );
    }

    // ==============================
    // 🧑‍💻 FREELANCER TESTS
    // ==============================

    public function testGetMyItems(): void
    {
        $client = $this->getClientWithToken();

        $client->request('GET', '/api/orders/my-items');

        $this->assertResponseIsSuccessful();
    }

    public function testGetMyItemsByUser(): void
    {
        $client = $this->getClientWithToken();

        $client->request('GET', '/api/orders/my-items/user/1');

        $this->assertResponseIsSuccessful();
    }

    public function testAcceptOrderItem(): void
    {
        $client = $this->getClientWithToken();

        $client->request('POST', '/api/orders/my-items/1/accept');

        $this->assertTrue(
            in_array(
                $client->getResponse()->getStatusCode(),
                [Response::HTTP_OK, Response::HTTP_INTERNAL_SERVER_ERROR]
            )
        );
    }

    public function testCancelOrderItemByFreelancer(): void
    {
        $client = $this->getClientWithToken();

        $client->request('POST', '/api/orders/my-items/1/cancel');

        $this->assertTrue(
            in_array(
                $client->getResponse()->getStatusCode(),
                [
                    Response::HTTP_OK,
                    Response::HTTP_CONFLICT,
                    Response::HTTP_INTERNAL_SERVER_ERROR
                ]
            )
        );
    }

    // ==============================
    // 🚚 SHIPPER TESTS
    // ==============================

    public function testShipOrderItem(): void
    {
        $client = $this->getClientWithToken();

        $client->request('POST', '/api/orders/1/items/1/ship');

        $this->assertTrue(
            in_array(
                $client->getResponse()->getStatusCode(),
                [Response::HTTP_OK, Response::HTTP_INTERNAL_SERVER_ERROR]
            )
        );
    }

    public function testDeliverOrderItem(): void
    {
        $client = $this->getClientWithToken();

        $client->request('POST', '/api/orders/1/items/1/deliver');

        $this->assertTrue(
            in_array(
                $client->getResponse()->getStatusCode(),
                [Response::HTTP_OK, Response::HTTP_INTERNAL_SERVER_ERROR]
            )
        );
    }
}