<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwapiService
{
    private $httpClient;
    private $url;
    private $log;

    public function __construct(string $url, LoggerInterface $logger)
    {
        $this->httpClient = new Client();
        $this->url = $url;
        $this->log = $logger;
    }

    public function get(string $slug): array
    {
        return $this->request(Request::METHOD_GET, $slug);
    }

    private function request(string $method, string $slug): array
    {
        try {
            $options = [
                'headers' => [
                    'Content-Type'  => 'application/json',
                ],
            ];

            $response = $this->httpClient->request($method, $this->url  . $slug, $options);
            return ['data' => json_decode($response->getBody()->getContents()), 'code' => Response::HTTP_OK];
        } catch (RequestException $guzzleException) {
            // Manejar excepciones específicas de Guzzle
            $response = $guzzleException->getResponse();
            $this->log->error("Guzzle request failed {$this->url}{$slug}", [
                'code' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents()
            ]);
            return ['data' => json_encode($response->getBody()->getContents()), 'code' => Response::HTTP_BAD_REQUEST];
        } catch (Exception $e) {
            $this->log->error("API request failed {$this->url}{$slug}", [
                'code' => $e->getCode(),
                'body' => $e->getMessage(),
            ]);
            return ['data' => json_encode($e->getMessage()), 'code' => Response::HTTP_BAD_REQUEST];
        }
    }

    public function getPeople(int $page = null): array
    {
        $slug = (is_null($page)) ? 'people/' : "people/?page={$page}";
        $response = $this->get($slug);
        return $response;
    }
}
