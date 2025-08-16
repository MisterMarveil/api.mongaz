<?php
namespace App\Action\Driver;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;
use App\Entity\DriverProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


#[AsController]
final class StartDelivery extends AbstractController
{
    #[Route(
        name: 'driver_start_delivery',
        path: '/api/drivers/current/start-delivery',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => DriverProfile::class,
            '_api_operation_name' => 'driver_start_delivery',
        ]
    )]
    public function __invoke(Request $request, #[CurrentUser] User $driver, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $order = $em->getRepository(Order::class)->find($data['order_id'] ?? null);

        if (!$order || $order->getDriver() !== $driver) {
            return new JsonResponse(['error' => 'Not allowed'], 403);
        }

        $order->markInProgress();
        $em->flush();

        return new JsonResponse(['status' => 'in_progress']);
    }
}
