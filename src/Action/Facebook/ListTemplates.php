<?php
namespace App\Action\Facebook;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Service\Facebook\GraphClient;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\FacebookSystem;

#[AsController]
final class ListTemplates
{
    #[Route(
        name: 'facebook_list_templates',
        path: '/api/facebook/templates',
        methods: ['GET'],
        defaults: [
            '_api_resource_class' => FacebookSystem::class,
            '_api_operation_name' => 'facebook_list_templates',
        ]
    )]
    public function __construct(private GraphClient $graph) {}

    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->graph->listTemplates());
    }
}
