<?php
namespace App\Action\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Entity\Order;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

#[AsController]
final class AssignDriver extends AbstractController
{
    #[Route(
        name: 'admin_assign_driver',
        path: '/api/admin/orders/{id}/assign-driver',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => Order::class,
             '_api_operation_name' => 'admin_assign_driver',
         ]
    )]
    public function __invoke(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $order = $em->getRepository(Order::class)->find($request->get('id'));
        $driver = $em->getRepository(User::class)->find($data['driver_id'] ?? null);

        if (!$order || !$driver) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        $order->assignToDriver($driver);
        $em->flush();

        return new JsonResponse(['status' => 'assigned']);
    }
}
