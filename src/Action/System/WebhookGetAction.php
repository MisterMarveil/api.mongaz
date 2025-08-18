<?php
namespace App\Action\System;

use App\Entity\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
final class WebhookGetAction
{
    #[Route(
        path: '/api/system/webhook',
        name: 'system_webhook_get',
        methods: ['GET'],
        defaults: [
            '_api_resource_class' => System::class,
            '_api_operation_name' => 'system_webhook_get'
        ]
    )]
    public function __invoke(Request $request): Response
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
}
