<?php
namespace App\Action\System;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Entity\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

#[AsController]
final class HealthCheck extends AbstractController
{
    #[Route(
        name: 'system_health',
        path: '/api/_health',
        methods: ['GET'],
        defaults: [
            '_api_resource_class' => System::class,
            '_api_operation_name' => 'system_health',
        ]
    )]
    public function __invoke(HubInterface $hub): JsonResponse    
    {
        //checking sse server health
        $update = new Update(
            'health_check',
            json_encode(['status' => 'test', 'timestamp' => time()])
        );
    
        try {
             $hub->publish($update);            
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'sse server health error', 'message' => $e->getMessage()]);
        }

        return new JsonResponse(['status' => 'ok', 'time' => date('c')]);
    }
}
