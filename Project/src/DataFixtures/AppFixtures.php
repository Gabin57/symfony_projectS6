<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Users
        $users = [
            ['email' => 'admin@test.com', 'role' => 'ROLE_ADMIN', 'name' => 'Admin'],
            ['email' => 'manager@test.com', 'role' => 'ROLE_MANAGER', 'name' => 'Manager'],
            ['email' => 'user@test.com', 'role' => 'ROLE_USER', 'name' => 'User'],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstname($userData['name']);
            $user->setLastname('Test');
            $user->setRoles([$userData['role']]);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
        }

        // Products
        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setName('Product ' . $i);
            $product->setDescription('Description for product ' . $i);
            $product->setPrice(mt_rand(10, 100));
            $product->setType('physical'); // Default type
            $manager->persist($product);
        }

        // Clients
        for ($i = 1; $i <= 5; $i++) {
            $client = new \App\Entity\Client();
            $client->setFirstname('Client' . $i);
            $client->setLastname('Doe');
            $client->setEmail('client' . $i . '@test.com');
            $client->setPhoneNumber('012345678' . $i);
            $client->setAddress($i . ' Rue de Test');
            $manager->persist($client);
        }

        $manager->flush();
    }
}
