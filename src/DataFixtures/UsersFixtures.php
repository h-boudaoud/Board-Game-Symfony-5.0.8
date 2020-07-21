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
        $newData = [] ;
        $username ='';
        foreach (User::ROLES as $role) {
            $username = str_replace('ROLE_', '', $role);
            $username = str_replace('_', '-', $username);
            $user = new User();
            $date = new \DateTime();
            $date->setDate(2001, 2, 3);
            $user->setUserName($username)
                ->setPassword(strstr('ADMIN', $username) ? '1Housn1.' : 'P@ssw0rd.')
                ->encodePassword($this->encoder)
                ->setFirstName($username)
                ->setEmail($username . '@board-game.biz.ht')
                ->setLastName(str_replace('-', ' ', $username))
                ->setBirthday($date)
                ->setRoles([$role]);

            $manager->persist($user);
            $newData[] = $user;
        }

        for ($i = 1; $i <= 30; $i++) {
            $user = new User();
            $date = new \DateTime();
            $date->setDate(2001, 2, 3);
            $user->setUserName($username.$i )
                ->setPassword('P@ssw0rd.')
                ->encodePassword($this->encoder)
                ->setFirstName($username.' '.$i )
                ->setEmail($username.'_'.$i . '@board-game.biz.ht')
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
//            dump("Exeption add user " .$e->getMessage());
//        }
//        dump(["User User_$i" => $user]);

        dump(['Users Fixtures' => Count($newData). " new Users in database"]);
    }

}
