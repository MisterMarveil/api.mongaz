<?php
namespace App\Action\Facebook;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Service\Facebook\GraphClient;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\FacebookSystem;

#[AsController]
final class SendMessage
{
    public function __construct(private GraphClient $graph) {}

    #[Route(
        name: 'facebook_send_message',
        path: '/api/facebook/messages',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => FacebookSystem::class,
            '_api_operation_name' => 'facebook_send_message',
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $in = json_decode($request->getContent(), true) ?? [];
        if (($in['type'] ?? 'text') === 'template') {
            $res = $this->graph->sendTemplate($in['to'], $in['template']);
        } else {
            $res = $this->graph->sendText($in['to'], $in['text']['body'] ?? '');
        }
        return new JsonResponse($res);
    }
}
