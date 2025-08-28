<?php
namespace App\Action\Facebook;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\FacebookSystem;
use App\Service\Facebook\AccountLinker;

#[AsController]
final class LinkAccount
{
    #[Route(
        name: 'facebook_link_account',
        path: '/api/facebook/link-account',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => FacebookSystem::class,
            '_api_operation_name' => 'facebook_link_account',
        ]
    )]
    public function __construct(private AccountLinker $linker) {}

    public function __invoke(Request $request): JsonResponse
    {
        $in = json_decode($request->getContent(), true) ?? [];
        $wabaId = $in['waba_id'] ?? null;
        $token  = $in['access_token'] ?? null;
        $phoneIds = $in['phone_ids'] ?? [];
        $tenantId = Uuid::v4()->toRfc4122(); // or your authenticated tenant

        $this->linker->storeInitialLink($tenantId, $wabaId, $phoneIds, $token);
        return new JsonResponse(['linked'=>true], 201);
    }
}
