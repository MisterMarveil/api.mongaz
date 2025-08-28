<?php
namespace App\Action\Facebook;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Entity\FacebookSystem;

#[AsController]
final class WebhookReceive
{
    #[Route(
        name: 'facebook_webhook_receive',
        path: '/api/facebook/webhook',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => FacebookSystem::class,
            '_api_operation_name' => 'facebook_webhook_receive',
        ]
    )]
    public function __construct(private LoggerInterface $logger) {}

    public function __invoke(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];
        // enqueue or process events (message status, inbound messages, template status)
        $this->logger->info('[FB] Webhook event', ['payload'=>$payload]);
        return new JsonResponse(['status'=>'ok']);
    }
}
