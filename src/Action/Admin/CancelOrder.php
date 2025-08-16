<?php
namespace App\Action\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;


#[AsController]
final class CancelOrder extends AbstractController
{
    #[Route(
        name: 'admin_cancel_order',
        path: '/api/admin/orders/{id}/cancel',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => Order::class,
            '_api_operation_name' => 'admin_cancel_order',
        ]
    )]
    public function __invoke(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $order = $em->getRepository(Order::class)->find($request->get('id'));

        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $order->cancelByAdmin($data['reason'] ?? null);
        $em->flush();

        return new JsonResponse(['status' => 'canceled']);
    }
}
