<?php
namespace App\Action\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[Route(
    name: 'reset_password',
    path: '/api/users/reset-password',
    methods: ['POST'],
    defaults: [
        '_api_resource_class' => User::class,
        '_api_operation_name' => 'password_reset',
    ]
)]
class ResetPasswordAction extends AbstractController
{
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $phone = $data['phone'] ?? null;
        $code = $data['code'] ?? null;
        $newPassword = $data['newPassword'] ?? null;

        if (!$phone || !$code || !$newPassword) {
            return new JsonResponse(['error' => 'phone, code and newPassword required'], 400);
        }

        $user = $userRepository->findOneBy(['phone' => $phone]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if (
            !$user->getResetPasswordCode() ||
            $user->getResetPasswordCode() !== $code ||
            $user->getResetPasswordCodeExpiresAt() < new \DateTimeImmutable()
        ) {
            return new JsonResponse(['error' => 'invalid or expired code'], 401);
        }

        $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
        $user->setResetPasswordCode(null);
        $user->setResetPasswordCodeExpiresAt(null);
        $userRepository->save($user, true);

        return new JsonResponse(['status' => 'password reset successful']);
    }
}
