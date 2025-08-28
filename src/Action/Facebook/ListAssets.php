<?php
namespace App\Action\Facebook;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Service\Facebook\GraphClient;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\FacebookSystem;

#[AsController]
final class ListAssets
{
    #[Route(
        name: 'facebook_list_assets',
        path: '/api/facebook/assets',
        methods: ['GET'],
        defaults: [
            '_api_resource_class' => FacebookSystem::class,
            '_api_operation_name' => 'facebook_list_assets',
        ]
    )]
    public function __construct(private GraphClient $graph) {}

    public function __invoke(): JsonResponse
    {
        $assets = $this->graph->listAssetsForCurrentTenant();
        return new JsonResponse($assets);
    }
}
