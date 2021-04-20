<?php

namespace App\DataFixtures;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
     private $passwordEncoder;
     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }
    public function load(ObjectManager $manager)
    {
      $user = new User();
      $user->setEmail("admin@example.com");
      $user->setPhone(0);
      $user->setFirstName("Administrator");
      $user->setRoles(["ROLE_SUPERADMIN"]);
      $user->setPassword($this->passwordEncoder->encodePassword(
             $user,
             'dispatch'
         ));
     $manager->persist($user);
     $manager->flush();
    }
}
