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
class ResendActivationAction extends AbstractController
{
    #[Route(
        name: 'resend_activation_code',
        path: '/api/users/resend-activation',
        methods: ['POST'],
        defaults: [
            '_api_resource_class' => User::class,
            '_api_operation_name' => 'resend_activation_code',
        ]
    )]
    public function __invoke(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $phone = $data['phone'] ?? null;

        if (!$phone) {
            return new JsonResponse(['error' => 'phone required'], 400);
        }

        $user = $userRepository->findOneBy(['phone' => $phone]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if ($user->isEnabled()) {
            return new JsonResponse(['error' => 'already activated'], 400);
        }

        // reuse old code if still valid
        if ($user->getActivationCode() && $user->getActivationCodeExpiresAt() > new \DateTimeImmutable()) {
            $code = $user->getActivationCode();
        } else {
            $code = random_int(100000, 999999);
            $user->setActivationCode((string)$code);
            $user->setActivationCodeExpiresAt(new \DateTimeImmutable('+10 minutes'));
        }

        $userRepository->save($user, true);

        // send SMS here...

        return new JsonResponse(['status' => 'code sent']);
    }
}
