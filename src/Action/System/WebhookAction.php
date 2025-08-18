<?php
namespace App\Action\System;

use App\Entity\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route(
    path: '/api/system/webhook',
    name: 'system_webhook',
    methods: ['GET', 'POST'],
    defaults: [
        '_api_resource_class' => System::class,
        '_api_operation_name' => 'system_webhook'
    ]
)]
final class WebhookAction
{
    public function __invoke(Request $request): Response
    {
        if ($request->isMethod('GET')) {
            return $this->handleVerification($request);
        }

        if ($request->isMethod('POST')) {
            return $this->handleWebhook($request);
        }

        return new Response('Method Not Allowed', Response::HTTP_METHOD_NOT_ALLOWED);
    }

    private function handleVerification(Request $request): Response
    {
        $mode = $request->query->get('hub.mode');
        $challenge = $request->query->get('hub.challenge');
        $token = $request->query->get('hub.verify_token');

        $verifyToken = $_ENV['VERIFY_TOKEN'] ?? null;

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return new Response($challenge, Response::HTTP_OK);
        }

        return new Response('Forbidden', Response::HTTP_FORBIDDEN);
    }

    private function handleWebhook(Request $request): JsonResponse
    {
        $timestamp = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $data = json_decode($request->getContent(), true);

        // Log webhook payload (replace with Monolog or DB save)
        file_put_contents(
            __DIR__ . '/../../../var/log/webhook.log',
            "\n\nWebhook received $timestamp\n" . json_encode($data, JSON_PRETTY_PRINT),
            FILE_APPEND
        );

        return new JsonResponse(['status' => 'ok'], Response::HTTP_OK);
    }
}
