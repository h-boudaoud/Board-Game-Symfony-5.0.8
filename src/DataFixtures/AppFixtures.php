<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // dd(getcwd ());
        // Api
        $file = getcwd() . '\public\asset\js\games.json';
        $jsonData = null;
        $jsonGames = [];
        $NewJsonGames = [];
        try {
            $jsonData = file_get_contents('https://www.boardgameatlas.com/api/search?name=Catan&pretty=true&client_id=SB1VGnDv7M');


            $jsonGames = json_decode($jsonData)
                ->games;
            dump('Success access: www.boardgameatlas.com/api');
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }
        if (Count($jsonGames)) {

            //unlink($file);
            $fileData = fopen($file, 'a+');
            fputs($fileData, json_encode($jsonGames));
            fclose($fileData);
        } else {
            dump('Error access: www.boardgameatlas.com/api');
            $jsonGames = json_decode(file_get_contents($file));
        }

        foreach ($jsonGames as $game) {
            $newGame = new Game();

            foreach ($game as $key => $value) {
                $key = $key != 'id' ? $key : 'game_Id';
                $dynamicMethodName = "set" . str_replace(
                        ' ', '',
                        ucwords(
                            strtolower(
                                str_replace('_', ' ', $key)
                            )
                        )
                    );
                if ($key == "rules_url") {
                    $value = (strlen ($value)<255)?$value:null;
                }
                if ($key == "discount" && $value) {

                    $value = ($value > 1 || $value < -1)
                        ? (($value * 100) % 100) / 100
                        : $value;

                    $value = $value < 0 ? $value : -$value;
                }

                if (method_exists($newGame, $dynamicMethodName) && $value) {

                    try {
                        $newGame->$dynamicMethodName($value);
                    } catch (\Exception $exception) {
                        dump($exception->getMessage());
                    }

//                    dump([
//                        'dynamicMethodName' => $dynamicMethodName,
//                        'value' => $value,
//                        'method_exists' => method_exists($newGame, $dynamicMethodName)
//                    ]);

                }

            }
            $newGame->setPublished(true);
            $manager->persist($newGame);
            $NewJsonGames[$newGame->getGameId()] = $newGame;
            dump(['newGame' => $newGame]);
        }




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
        }


        $manager->flush();
    }
}
