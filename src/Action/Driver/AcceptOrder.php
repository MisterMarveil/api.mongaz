<?php
namespace App\Action\Driver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Order\Entity\Order;
use App\Domain\User\Entity\User;
use App\Domain\User\Entity\DriverProfile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class AcceptOrder extends AbstractController
{
    #[Route(
        name: 'driver_accept_order',
        path: '/api/drivers/current/accept',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => DriverProfile::class,
            '_api_operation_name' => 'driver_accept_order',
        ]
     )]
    public function __invoke(Request $request, #[CurrentUser] User $driver, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $order = $em->getRepository(Order::class)->find($data['order_id'] ?? null);

        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }

        if ($order->getStatus() !== Order::STATUS_AWAITING_ASSIGNMENT) {
            return new JsonResponse(['error' => 'Order not available'], 400);
        }

        $order->assignToDriver($driver);
        $em->flush();

        return new JsonResponse(['status' => 'accepted', 'order_id' => $order->getId()]);
    }
}
