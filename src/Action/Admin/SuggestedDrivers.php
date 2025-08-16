<?php
namespace App\Action\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Domain\Order\Entity\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\User\Entity\User;


[#AsController]
final class SuggestedDrivers extends AbstractController
{
    #[Route(
        name: 'admin_suggested_drivers',
        path: '/api/admin/orders/{id}/suggested-drivers',
        methods: ['GET'],
        defaults: [
            '_api_resource_class' => Order::class,
            '_api_operation_name' => 'admin_suggested_drivers',
        ]
    )]
    public function __invoke(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $order = $em->getRepository(Order::class)->find($request->get('id'));

        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }

        $drivers = $em->getRepository(User::class)->findAvailableNearby($order->getPickupLocation());

        return new JsonResponse(['drivers' => $drivers]);
    }
}
