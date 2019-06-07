<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    
    private $passwordEncoder;
    
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $users = [
            ["abouchereau@umanis.com","Anthony","umanis"],
            ["fbouazdi@umanis.com","Farid","umanis"],
            ["jplantier@umanis.com","Josselin","umanis"],
            ];
        foreach($users as $eachUser) {
            $user = new User();
            $user->setEmail($eachUser[0]);
            $user->setFullname($eachUser[1]);
            $user->setCreatedAt(new \DateTime());
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $eachUser[2]
            ));
           $manager->persist($user);
        }
        $manager->flush();
    }
    


}
