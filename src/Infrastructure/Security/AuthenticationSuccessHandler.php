<?php
namespace App\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use App\Entity\User;

class AuthenticationSuccessHandler
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data['user'] = [
            'id' => (string) $user->getId(),
            'phone' => $user->getPhone(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
        ];

        $event->setData($data);
    }
}
