<?php
namespace App\Action\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[Route(
    name: 'request_password_reset',
    path: '/api/users/request-password-reset',
    methods: ['POST'],
    defaults: [
        '_api_resource_class' => User::class,
        '_api_operation_name' => 'password_reset_request',
    ]
)]
class RequestPasswordResetAction extends AbstractController
{
    public function __invoke(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $phone = $data['phone'] ?? null;

        if (!$phone) {
            return new JsonResponse(['error' => 'Phone number required'], 400);
        }

        $user = $userRepository->findOneBy(['phone' => $phone]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if (!$user->isActive()) {
            return new JsonResponse(['error' => 'user not activated'], 400);
        }

        // generate reset code
        $code = random_int(100000, 999999);
        $user->setResetPasswordCode((string)$code);
        $user->setResetPasswordCodeExpiresAt(new \DateTimeImmutable('+10 minutes'));

        $userRepository->save($user, true);

        // here you would trigger an SMS service...

        return new JsonResponse(['status' => 'reset code sent']);
    }
}
