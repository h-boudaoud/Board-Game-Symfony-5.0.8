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
            $jsonData = file_get_contents('https://www.boardgameatlas.com/api/search?client_id=SB1VGnDv7M');


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
            $jsonGames = json_decode(file_get_contents($file))->games;
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
                    $value = (strlen($value) < 255) ? $value : null;
                }
                if ($key == "name" && empty($value)) {
                    dd($game);
                    $value = (Count($game->names)>0)?$game->names[0]:$game->id;
                    $game->name=$value;
                }

                if ($key == "discount" && $value) {

                    $value = ($value > .8 || $value < -.8)?.5:($value > .5 || $value < -.5)
                        ? ((($value+.7) * 100) % 100) / 100
                        : $value;

                    $value = $value < 0 ? $value : -$value;
                }

                if ($key == "msrp" && empty(intval($value))) {
                    $min =500 ;
                    $max = 10000;
                    $value = random_int($min, $max) / 100;
                    if(empty(intval($game->price))){
                        $min =intval($value*80) ;
                        $max = intval($value*120);
                        $game->price = random_int($min, $max) / 100;
                    }
                    dump(['$key' => $key, 'value' => $value]);
                }

                if ($key == "price" && empty(intval($value))) {
                    if(empty(intval($game->msrp))){
                        $min =500 ;
                        $max = 10000;
                        $game->msrp = random_int($min, $max) / 100;
                    }

                    $min =intval($game->msrp*80) ;
                    $max = intval($game->msrp*120);
                    $value = random_int($min, $max) / 100;
                }

                if (method_exists($newGame, $dynamicMethodName) && $value) {

                    try {
                        $newGame->$dynamicMethodName($value);
                    } catch (\Exception $exception) {
                        dump([
                            'Error message : '=>$exception->getMessage(),
                            'dynamicMethodName'=>$dynamicMethodName,
                            'value'=>$value
                        ]);
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

////  Update big data
//namespace App\DataFixtures;
//
//use App\Entity\Game;
//use App\Entity\User;
//use Doctrine\Bundle\FixturesBundle\Fixture;
//use Doctrine\Persistence\ObjectManager;
//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
//
//class AppFixtures extends Fixture
//{
//    private $encoder;
//
//    public function __construct(UserPasswordEncoderInterface $encoder)
//    {
//        $this->encoder = $encoder;
//    }
//
//    public function load(ObjectManager $manager)
//    {
//        // dd(getcwd ());
//        // Api
//        $file = getcwd() . '\public\asset\js\games';
//        $fileType = '.json';
//        $jsonData = null;
//        $jsonGames = null;
//        $NewJsonGames = [];
//        $newUrlGames = [];
//        $newGames = [];
//        $limit = 100;
//        $skip = 0;
//        $url = "https://www.boardgameatlas.com/api/search?client_id=SB1VGnDv7M&limit=$limit";
//        $endWhile = true;
//        do {
//            try {
//                $jsonData = file_get_contents($url . ($skip ? "&skip=$skip" : ''));
//                $NewJsonGames = json_decode($jsonData)->games;
//                $endWhile = Count($NewJsonGames) == 0;
//                dump("Success access: $url" . ($skip ? "&skip=$skip" : ''));
//                $newUrlGames = array_merge($newUrlGames, $NewJsonGames);
//                $skip += Count($NewJsonGames);
//                $endWhile = Count($NewJsonGames) == 0;
//
//            } catch (\Exception $exception) {
//                dump("Error access:.$url" . ($skip ? "&skip=$skip" : ''));
//                dump($exception->getMessage());
//                $endWhile = true;
//            }
//            dump(['skip' => $skip]);
//        } while ($endWhile == false);
//
//        $jsonGames = Count($newUrlGames) ? $newUrlGames : json_decode(file_get_contents($file . $fileType));
//
//        foreach ($jsonGames as $game) {
//            $newGame = new Game();
//            $game->game_id = $game->id;
//
//            foreach ($game as $key => $value) {
//                $value = $key != 'id' ? $value : null;
//                $dynamicMethodName = "set" . str_replace(
//                        ' ', '',
//                        ucwords(
//                            strtolower(
//                                str_replace('_', ' ', $key)
//                            )
//                        )
//                    );
//                if ($key == "rules_url") {
//                    $value = (strlen($value) < 255) ? $value : null;
//                }
//                if ($key == "name" && empty($value)) {
//                    dd($game);
//                    $value = (Count($game->names) > 0) ? $game->names[0] : $game->id;
//                    $game->name = $value;
//                }
//
//                if ($key == "discount" && $value) {
//
//                    $value = ($value > .8 || $value < -.8) ? .5 : ($value > .5 || $value < -.5)
//                        ? ((($value + .7) * 100) % 100) / 100
//                        : $value;
//
//                    $value = $value < 0 ? $value : -$value;
//                }
//
//                if ($key == "msrp" && empty(intval($value))) {
//                    $min = 500;
//                    $max = 10000;
//                    $value = random_int($min, $max) / 100;
//                    if (empty(intval($game->price))) {
//                        $min = intval($value * 80);
//                        $max = intval($value * 120);
//                        $game->price = random_int($min, $max) / 100;
//                    }
//                    dump(['$key' => $key, 'value' => $value]);
//                }
//
//                if ($key == "price" && empty(intval($value))) {
//                    if (empty(intval($game->msrp))) {
//                        $min = 500;
//                        $max = 10000;
//                        $game->msrp = random_int($min, $max) / 100;
//                    }
//
//                    $min = intval($game->msrp * 80);
//                    $max = intval($game->msrp * 120);
//                    $value = random_int($min, $max) / 100;
//                }
//
////                if ($key == "msrp" || $key == "price") {
////                    dump([
////                        'Id' => $game->id,
////                        '$key' => $key,
////                        'value' => $value,
////                        'empty($value*100)'=> empty($value*100),
////                        'msrp' => $newGame->getMsrp(),
////                        'price' => $newGame->getPrice()
////                    ]);
////
////                }
//
//
//                if (method_exists($newGame, $dynamicMethodName) && $value) {
//                    if ($value != $game->$key) {
//                        $game->$key = $value;
//                    }
//                    try {
//                        $newGame->$dynamicMethodName($value);
//                    } catch (\Exception $exception) {
//
//                        dump([
//                            'Error message' => $exception->getMessage(),
//                            'key' => $key,
//                            'value' => $value
//                        ]);
//                    }
//
////                    dump([
////                        'dynamicMethodName' => $dynamicMethodName,
////                        'value' => $value,
////                        'method_exists' => method_exists($newGame, $dynamicMethodName)
////                    ]);
//
//                }
//
//            }
//            $newGame->setPublished(true);
//            if (empty($newGame->getName())) {
//                $newGame->setName((Count($game->names) > 0) ? $game->names[0] : $game->id);
//            }
//            $manager->persist($newGame);
//            $NewJsonGames[$newGame->getGameId()] = $newGame;
//            $newGames[$game->id] = $game;
//            // dump(['newGame' => $newGame]);
//        }
//        if (Count($newUrlGames)) {
//
//            unlink($file . $fileType);
//            $fileData = fopen($file . $fileType, 'a+');
//            fputs($fileData, json_encode($newGames));
//            fclose($fileData);
//
//            $file .= (new \DateTime())->format('-Y-m-d-H-i-s');
//            $fileData = fopen($file . $fileType, 'a+');
//            fputs($fileData, json_encode($newGames));
//            fclose($fileData);
//        }
//
//
//        foreach (User::ROLES as $role) {
//
//            $username = str_replace('ROLE_', '', $role);
//            $username = str_replace('_', '-', $username);
//            $user = new User();
//            $date = new \DateTime();
//            $date->setDate(2001, 2, 3);
//            $user->setUserName($username)
//                ->setPassword(strstr('ADMIN', $username) ? '1Housn1.' : 'P@ssw0rd.')
//                ->encodePassword($this->encoder)
//                ->setFirstName($username)
//                ->setEmail($username . '@board-game.biz.ht')
//                ->setLastName(str_replace('-', ' ', $username))
//                ->setBirthday($date)
//                ->setRoles([$role]);
//
//            $manager->persist($user);
//        }
//
//
//        $manager->flush();
//    }
//}

