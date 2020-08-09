<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UsersFixtures extends Fixture
{
    private $encoder;

    // public const USERS_REFERENCE = 'users';


    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $newData = [];
        $username = '';
        foreach (User::ROLES as $role) {
            if ($role != 'ROLE_USER') {
                $username = str_replace('ROLE_', '', $role);
                $username = str_replace('_', '-', $username);
                $user = new User();
                $date = new \DateTime();
                $date->setDate(2001, 2, 3);
                $user->setUserName($username)
                    ->setPassword('P@ssw0rd.')
                    ->setConfirmPassword('P@ssw0rd.')
                    ->encodePassword($this->encoder)
                    ->setFirstName($username)
                    ->setEmail($username . '@board-game.biz.ht')
                    ->setLastName(str_replace('-', ' ', $username))
                    ->setBirthday($date)
                    ->setRoles([$role]);

                $manager->persist($user);
                $newData[] = $user;
            }
        }

        $emailDomains = [
            'gmail.com',
            'yahoo.com',
            'hotmail.com',
            'aol.com',
            'hotmail.co.uk',
            'hotmail.fr',
            'msn.com',
            'yahoo.fr',
            'live.com',
            'free.fr',
            'gmx.de',
            'web.de',
            'outlook.com',
            'hotmail.it',
            'live.fr',
            'googlemail.com',
            'facebook.com',
            'mac.com'
        ];

        $username = 'user';
        for ($i = 1; $i <= 30; $i++) {
            shuffle($emailDomains);  // Shuffle uses a pseudo random number generator
            $user = new User();
            $date = new \DateTime();
            $date->setDate(2001, 2, 3);
            $user->setUserName($username . $i)
                ->setPassword('P@ssw0rd.')
                ->setConfirmPassword('P@ssw0rd.')
                ->encodePassword($this->encoder)
                ->setFirstName($username . ' ' . $i)
                ->setEmail($username . '_' . $i . '@' . $emailDomains[0])
                ->setLastName($username)
                ->setBirthday($date);

            $manager->persist($user);
            $this->addReference("User_$i", (object)$user);
            $newData[] = $user;
        }

        $manager->flush();

//        $i = random_int(1,100);
//        $user=null;
//        try
//        {
//            $user = $this->getReference("User_$i");
//        }catch (\Exception $e){
//            dump("Exception add user " .$e->getMessage());
//        }
//        dump(["User User_$i" => $user]);

        dump(['Users Fixtures' => Count($newData) . " new Users in database"]);
    }


}
