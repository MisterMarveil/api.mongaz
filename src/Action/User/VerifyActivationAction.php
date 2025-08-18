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
class VerifyActivationAction extends AbstractController
{
    #[Route(
        name: 'verify_activation',
        path: '/api/users/verify-activation',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => User::class,
            '_api_operation_name' => 'verification',
        ]
    )]
    public function __invoke(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $phone = $data['phone'] ?? null;
        $code = $data['code'] ?? null;

        if (!$phone || !$code) {
            return new JsonResponse(['error' => 'phone and code required'], 400);
        }

        $user = $userRepository->findOneBy(['phone' => $phone]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if (!$user->getActivationCode()) {
            return new JsonResponse(['error' => 'no activation code'], 400);
        }

        if ($user->getActivationCode() !== $code) {
            return new JsonResponse(['error' => 'invalid code'], 401);
        }

        if ($user->getActivationCodeExpiresAt() < new \DateTimeImmutable()) {
            return new JsonResponse(['error' => 'activation code expired'], 410);
        }

        $user->setIsEnabled(true);
        $user->setEnabledAt(new \DateTimeImmutable());
        $user->setActivationCode(null);
        $user->setActivationCodeExpiresAt(null);

        $userRepository->save($user, true);

        return new JsonResponse(['status' => 'activated']);
    }
}
