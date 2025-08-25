<?php
namespace App\Action\System;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
class GetEnvironmentVariables extends AbstractController
{
    private array $allowedParameters = [
        'MAPBOX_TOKEN',
        'MERCURE_SUBSCRIBER_TOKEN'
    ];


    #[Route(
        name: 'get_env_vars',
        path: '/api/system/env-vars',
        methods: ['GET'],
        defaults: [
            '_api_resource_class' => 'App\Entity\System',
            '_api_operation_name' => 'get_env_vars',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function __invoke(Request $request): JsonResponse
    {
        $requested = $request->query->all('names'); // expects names[]=MAPBOX_TOKEN&names[]=GOOGLE_TOKEN
        $result = [];

        foreach ($requested as $param) {
            if (in_array($param, $this->allowedParameters, true)) {
                $result[$param] = $_ENV[$param] ?? getenv($param) ?? null;
            } else {
               return new JsonResponse('parameter not allowed', 403);
            }
        }

        return new JsonResponse($result);
    }
}
