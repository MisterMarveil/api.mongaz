<?php
namespace App\Action\Facebook;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Entity\FacebookSystem;

#[AsController]
final class WebhookVerify
{

    #[Route(
        name: 'facebook_webhook_verify',
        path: '/api/facebook/webhook',
        methods: ['GET'],
        defaults: [
            '_api_resource_class' => FacebookSystem::class,
            '_api_operation_name' => 'facebook_webhook_verify',
        ]
    )]
    public function __invoke(Request $request, #[Autowire('%env(FB_VERIFY_TOKEN)%')] string $verifyToken): Response
    {
        if ($request->query->get('hub.mode') === 'subscribe'
            && $request->query->get('hub.verify_token') === $verifyToken) {
            return new Response($request->query->get('hub.challenge'), 200, ['Content-Type'=>'text/plain']);
        }
        return new Response('Forbidden', 403);
    }
}
