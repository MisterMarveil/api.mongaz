<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // --- Admin User ---
        $admin = new User();
        $admin->setPhone('+237690000001')
              ->setName('System Admin')              
              ->setRoles(['ROLE_ADMIN'])
              ->activateAccount();

        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin123')
        );
        $manager->persist($admin);

        // --- Driver User ---
        $driver = new User();
        $driver->setPhone('+237690000002')
               ->setName('Default Driver')               
               ->setRoles(['ROLE_DRIVER'])
               ->activateAccount();

        $driver->setPassword(
            $this->passwordHasher->hashPassword($driver, 'driver123')
        );
        $manager->persist($driver);

        // --- Client User ---
        $client = new User();
        $client->setPhone('+237690000003')
               ->setName('Test Client')               
               ->setRoles(['ROLE_USER'])
               ->activateAccount();

        $client->setPassword(
            $this->passwordHasher->hashPassword($client, 'client123')
        );
        $manager->persist($client);

        // Save all
        $manager->flush();
    }
}
