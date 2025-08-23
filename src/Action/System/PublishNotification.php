<?php
namespace App\Action\System;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use App\Entity\System;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
class PublishNotification
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

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
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? null;
        $role    = $data['role'] ?? null;
        $userIds = $data['userIds'] ?? [];
        $topic   = $data['topic'] ?? 'user';

        if (!$message) {
            return new JsonResponse(['error' => 'Message is required'], 400);
        }

        $topics = [];

        // Consistent naming protocol
        if ($role) {
            $topics[] = "system:role:{$role}";
        }

        if (!empty($userIds)) {
            foreach ($userIds as $uuid) {
                $topics[] = "system:user:{$uuid}";
            }
        }

        // Default global notification
        if (!$role && empty($userIds)) {
            $topics[] = "system:all";
        }

        // Publish to Mercure hub
        foreach ($topics as $t) {
            $update = new Update(
                $t,
                json_encode([
                    'topic' => $t,
                    'message' => $message,
                    'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM)
                ])
            );
            $this->hub->publish($update);
        }

        return new JsonResponse([
            'status' => 'Notification published',
            'topics' => $topics
        ], 200);
    }
}
