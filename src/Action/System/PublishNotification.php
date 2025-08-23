<?php

namespace App\Action\System;

use App\Entity\System; // your "fake" ApiResource class namespace
use App\Message\NotificationMessage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
final class PublishNotification
{
    public function __construct(private MessageBusInterface $bus) {}

    #[Route(
        path: '/api/system/notify',
        name: 'system_notify',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => System::class,
            '_api_operation_name' => 'system_notify',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function __invoke(Request $request): JsonResponse
    {
        $data    = json_decode($request->getContent(), true) ?? [];
        $message = $data['message'] ?? null;
        $role    = $data['role'] ?? null;            // ex: ROLE_DRIVER
        $userIds = $data['userIds'] ?? [];           // array of user UUIDs
        $event   = $data['event'] ?? null;           // ex: "order.assigned"
        $extra   = $data['extra'] ?? [];             // arbitrary payload keys

        if (!$message) {
            return new JsonResponse(['error' => 'Message is required'], 400);
        }

        // --- Topic naming protocol ---
        // Global:           system:all
        // Role-based:       system:role:{ROLE}
        // User-specific:    system:user:{uuid}
        // Entity-specific:  system:entity:{name}:{id}  (future use)
        $topics = [];
        if ($role) {
            $topics[] = "system:role:{$role}";
        }
        if (!empty($userIds)) {
            foreach ($userIds as $uuid) {
                $topics[] = "system:user:{$uuid}";
            }
        }
        if (!$role && empty($userIds)) {
            $topics[] = 'system:all';
        }

        // Construct payload
        $payload = array_merge([
            'message' => $message,
        ], $extra);

        // Dispatch to the bus (async via RabbitMQ)
        $this->bus->dispatch(new NotificationMessage($topics, $payload, $event));

        // 202: request accepted / queued
        return new JsonResponse([
            'status' => 'queued',
            'topics' => $topics,
        ], 202);
    }
}
