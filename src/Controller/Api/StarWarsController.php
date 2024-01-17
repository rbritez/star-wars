<?php

namespace App\Controller\Api;

use App\DTO\PeopleDTO;
use App\Services\SwapiService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class StarWarsController extends AbstractController
{
    private $swapiService;
    private $cacheRemember;

    public function __construct(SwapiService $swapiService, ParameterBagInterface $globalParams)
    {
        $this->swapiService = $swapiService;
        $this->cacheRemember = (int) $globalParams->get('app.cache_remember');
    }

    #[Route('api/v1/people', name: 'people', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        $page = 1;
        $dataResult = [];
        try {
            $cache = new FilesystemAdapter();
            $cacheData = $cache->get('my_cache', function(ItemInterface $cache) use ($page, $dataResult) {
                $cache->expiresAfter($this->cacheRemember);
                do {
                    $dataResponse = (object) $this->swapiService->getPeople($page);
                    $page++;
                    $dataResult = array_merge($dataResult, $dataResponse->data->results);
                } while ($dataResponse->code == Response::HTTP_OK && !is_null($dataResponse->data->next));
                return (new PeopleDTO($dataResult))->data;
            });
            return new JsonResponse(['data' => $cacheData, 'code' => Response::HTTP_OK, 'message' => 'success']);
        } catch (Exception $e) {
            return new JsonResponse(['data' => [],'code' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => $e->getMessage()]);
        }
    }
}
