<?php
namespace App\Action\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;

#[AsController]
class VerifyResetPasswordCode
{
    public function __construct(private UserRepository $userRepository,private EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    #[Route(
        name: 'verify_reset_password_code',
        path: '/api/users/verify-reset-password-code',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => User::class,
            '_api_operation_name' => 'verify_reset_password_code',
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $phone = $data['phone'] ?? null;
        $code  = $data['code'] ?? null;

        if (!$phone || !$code) {
            return new JsonResponse(['error' => 'Phone and code are required'], 400);
        }

        $user = $this->userRepository->findOneBy(['phone' => $phone]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if (!$user->getResetPasswordCode() || !$user->getResetPasswordCodeExpiresAt()) {
            return new JsonResponse(['error' => 'No reset password request found'], 400);
        }

        // Check expiry
        if ($user->getResetPasswordCodeExpiresAt() < new \DateTimeImmutable()) {
            return new JsonResponse(['error' => 'Reset password code expired'], 401);
        }

        // Check code
        if ($user->getResetPasswordCode() !== $code) {
            return new JsonResponse(['error' => 'Invalid reset password code'], 401);
        }

        // Success: mark as verified (optional: clear code)
        $user->setResetPasswordCode(null);
        $user->setResetPasswordCodeExpiresAt(null);
        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse(['status' => 'Reset password code verified successfully'], 200);
    }
}
