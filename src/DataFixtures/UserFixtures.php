<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{

    public function __construct(UserPasswordEncoderInterface $password_encoder)
    {
        $this->password_encoder = $password_encoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach($this->getUserData() as [$name, $last_name, $email, $password, $roles])
        {
            $user = new User();
            $user->setName($name);
            $user->setLastName($last_name);
            $user->setPassword($this->password_encoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            ['Susie', 'Robson', 'susie@sle.com', '12345', ['ROLE_ADMIN']],
            ['Henry', 'Robson', 'henandsue@sle.com', '12345', ['ROLE_USER']],
            ['Joanne', 'Sarkozi', 'joandjo@sle.com', '12345', ['ROLE_USER']],
            ['Billy', 'Eade', 'billyandrachel@sle.com', '12345', ['ROLE_USER']],
            ['Arnold', 'Schwarzenegger', 'arnie@sle.com', '12345', ['ROLE_USER']],
            ['Bruce', 'Willias', 'bruce@sle.com', '12345', ['ROLE_USER']]
        ];
    }
}
