<?php
namespace App\Action\Driver;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\User\Entity\DriverProfile;



#[AsController]
final class ConfirmDelivery extends AbstractController
{
    #[Route(
        name: 'driver_confirm_delivery',
        path: '/api/drivers/current/confirm-delivery',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => DriverProfile::class,
            '_api_operation_name' => 'driver_confirm_delivery',
        ]
     )]
    public function __invoke(Request $request, #[CurrentUser] User $driver, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $order = $em->getRepository(Order::class)->find($data['order_id'] ?? null);

        if (!$order || $order->getDriver() !== $driver) {
            return new JsonResponse(['error' => 'Not allowed'], 403);
        }

        $order->markDelivered();
        $em->flush();

        return new JsonResponse(['status' => 'delivered']);
    }
}
