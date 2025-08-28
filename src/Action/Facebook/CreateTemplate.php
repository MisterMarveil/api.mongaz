<?php
namespace App\Action\Facebook;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Service\Facebook\GraphClient;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\FacebookSystem;

#[AsController]
final class CreateTemplate
{
    #[Route(
        name: 'facebook_create_template',
        path: '/api/facebook/templates',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => FacebookSystem::class,
            '_api_operation_name' => 'facebook_create_template',
        ]
    )]
    public function __construct(private GraphClient $graph) {}

    public function __invoke(Request $request): JsonResponse
    {
        $in = json_decode($request->getContent(), true) ?? [];
        $tpl = $this->graph->createTemplate(
            $in['name'], $in['category'], $in['language'], $in['components'] ?? []
        );
        return new JsonResponse($tpl, 201);
    }
}
