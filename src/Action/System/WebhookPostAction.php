<?php
namespace App\Action\System;

use App\Entity\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
final class WebhookPostAction
{
    #[Route(
        path: '/api/system/webhook',
        name: 'system_webhook_post',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => System::class,
            '_api_operation_name' => 'system_webhook_post'
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {        
        $timestamp = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $data = json_decode($request->getContent(), true);

        // Log webhook payload (replace with Monolog or DB save)
        file_put_contents(
            __DIR__ . '/../../../var/log/webhook.log',
            "\n\nWebhook received $timestamp\n" . json_encode($data, JSON_PRETTY_PRINT),
            FILE_APPEND
        );

        return new JsonResponse(['status' => 'ok'], JsonResponse::HTTP_OK);
    }
}
