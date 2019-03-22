<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $admin = new \App\Entity\User();
        $admin->setUsername('admin@example.com');
        $admin->setEmail('admin@example.com');
        $admin->setPlainPassword('123456');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $user = new \App\Entity\User();
        $user->setUsername('user@example.com');
        $user->setEmail('user@example.com');
        $user->setPlainPassword('123456');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $manager->flush();
    }
}
