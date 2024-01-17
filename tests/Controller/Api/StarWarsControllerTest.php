<?php

namespace App\Tests\Controller\Api;

use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\MockHttpClient;


class StarWarsControllerTest extends WebTestCase
{
    private $faker;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        //creamos un mock del servicio de swapi para evitar pegar al sitio real en los test
        $this->client = static::createClient(['httpClient' => new MockHttpClient()]);
        $this->client->getContainer()->set('App\Services\SwapiService', $this->createMockSwapiService());
    }

    public function testIndex()
    {
        //llamamos a nuestra api
        $this->client->request('GET', '/api/v1/people');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('code', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('success', $responseData['message']);
    }
private function createMockSwapiService()
    {
        $mockSwapiService = $this->createMock('App\Services\SwapiService');
        $mockSwapiService->method('getPeople')->willReturn($this->getPeople());
        return $mockSwapiService;
    }

    private function getPeople()
    {
        return [
            'data' => (object) [
                'next' => null,
                'count' => 1,
                'previous' => null,
                'results' => [
                    (object) [
                        "name" => $this->faker->name,
                        "birth_year" => $this->faker->lexify('?????'),
                        "height" => $this->faker->numberBetween(150, 200),
                        "mass" => $this->faker->numberBetween(50, 200),
                        "eye_color" => $this->faker->colorName,
                        "skin_color" => $this->faker->colorName,
                        "gender" => $this->faker->randomElement(['male', 'female','n/a']),
                        "created" => $this->faker->dateTimeThisDecade->format('Y-m-d H:i:s')
                    ]
                ]
            ]
        ];
    }
}
