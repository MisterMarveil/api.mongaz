<?php
namespace App\Action\Facebook;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Service\Facebook\TokenService;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\FacebookSystem;

#[AsController]
final class ExchangeToken
{

    #[Route(
        name: 'facebook_exchange_token',
        path: '/api/facebook/exchange-token',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => FacebookSystem::class,
            '_api_operation_name' => 'facebook_exchange_token',
        ]
    )]
    public function __construct(private TokenService $tokens) {}

    public function __invoke(Request $request): JsonResponse
    {
        $in = json_decode($request->getContent(), true) ?? [];
        $longLived = $this->tokens->exchangeToLongLived($in['access_token'] ?? '');
        $this->tokens->storeLongLivedForCurrentTenant($longLived);
        return new JsonResponse(['stored'=>true]);
    }
}
