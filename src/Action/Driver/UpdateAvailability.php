<?php
namespace App\Action\Driver;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Domain\User\Entity\User;
use App\Domain\User\Entity\DriverProfile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;


#[AsController]
final class UpdateAvailability extends AbstractController
{
    #[Route(
       name: 'driver_update_availability',
       path: '/api/drivers/current/availability',
       methods: ['POST'],
       defaults: [
           '_api_resource_class' => DriverProfile::class,
           '_api_operation_name' => 'driver_update_availability',
       ]
    )]
    public function __invoke(Request $request, #[CurrentUser] User $driver, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $driver->getProfile()->setAvailable((bool) $data['available']);
        $em->flush();

        return new JsonResponse(['status' => 'availability_updated']);
    }
}
