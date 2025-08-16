<?php
namespace App\Action\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Domain\Order\Entity\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;


#[AsController]
final class Kpis extends AbstractController
{
   #[Route(
        name: 'admin_kpis',
        path: '/api/admin/kpis',
        methods: ['GET'],
        defaults: [
            '_api_resource_class' => Order::class,
            '_api_operation_name' => 'admin_kpis',
        ]
    )]
    public function __invoke(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $from = new \DateTime($request->query->get('from'));
        $to   = new \DateTime($request->query->get('to'));

        $stats = $em->getRepository(Order::class)->computeKpis($from, $to);

        return new JsonResponse($stats);
    }
}
