<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\Mechanic;
use App\Entity\Review;
use App\Entity\User;
use App\Service\UploadDataService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

// use Doctrine\Common\Persistence\ObjectManager;

class GamesFixtures extends Fixture implements DependentFixtureInterface
{
    private $data;

    public function __construct(UploadDataService $dataService)
    {
        $url = 'https://www.boardgameatlas.com/api/search?client_id=JLBr5npPhV';
        $this->data = $dataService->uploadDataFromApi($url, 'games');

    }

    public function load(ObjectManager $manager)
    {
        // dd(Count($this->gamesData));
        $newData = [];

        foreach ($this->data as $game) {
            $new = new Game();

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
                    $value = (Count($game->names) > 0) ? $game->names[0] : $game->id;
                    $game->name = $value;
                }

                if ($key == "discount" && $value) {

                    $value = ($value > .8 || $value < -.8) ? .5 : ($value > .5 || $value < -.5)
                        ? ((($value + .7) * 100) % 100) / 100
                        : $value;

                    $value = $value < 0 ? $value : -$value;
                }

                if ($key == "msrp" && empty(intval($value))) {
                    $min = 500;
                    $max = 10000;
                    $value = random_int($min, $max) / 100;
                    if (empty(intval($game->price))) {
                        $min = intval($value * 80);
                        $max = intval($value * 120);
                        $game->price = random_int($min, $max) / 100;
                    }
                    // dump(['$key' => $key, 'value' => $value]);
                }

                if ($key == "price" && empty(intval($value))) {
                    if (empty(intval($game->msrp))) {
                        $min = 500;
                        $max = 10000;
                        $game->msrp = random_int($min, $max) / 100;
                    }

                    $min = intval($game->msrp * 80);
                    $max = intval($game->msrp * 120);
                    $value = random_int($min, $max) / 100;
                }
                if ($key == "categories") {
                    foreach ($value as $category) {
                        $categoryId = $category->id;
                        $new->addCategory($this->getReference('Category_' . $categoryId));
                    }
                    $value = null;
                }
                if ($key == "mechanics") {
                    foreach ($value as $mechanic) {
                        $mechanicId = $mechanic->id;
                        $mechanic = $this->getReference("Mechanic_$mechanicId");
                        // dump(["Mechanic_$mechanicId"=>$mechanic]);
                        $new->addMechanic($mechanic);
                    }
                    $value = null;
                }


                if (method_exists($new, $dynamicMethodName) && $value) {

                    try {
                        $new->$dynamicMethodName($value);
                    } catch (\Exception $exception) {
                        dump([
                            'Error message : ' => $exception->getMessage(),
                            'dynamicMethodName' => $dynamicMethodName,
                            'value' => $value
                        ]);
                    }


//                    if($key === 'mechanic_Id') {
//                        dd([
//                            'dynamicMethodName' => $dynamicMethodName,
//                            'value' => $value,
//                            'method_exists' => method_exists($new, $dynamicMethodName)
//                        ]);
//                    }

                }

            }
            $new->setPublished(random_int(1, 9) % 5 != 2);
            $manager->persist($new);
            $manager->flush();
            $this->loadReview($manager, $new);
            // dump([$newGame->getId()=>$newGame->getGameId().' - '.$newGame->getName()]);
            $newData[$new->getGameId()] = $new;
        }

        dump(['Games Fixtures' => Count($newData) . " new games in database"]);

    }

    public function getDependencies()
    {
        return array(
            CategoriesFixtures::class,
            MechanicsFixtures::class,
        );
    }

    // Upload reviews

    private function loadReview(ObjectManager $manager, Game $game)
    {
        // dump("Reviews".($game->getName()));
        $limit = random_int(0, 30);
        $url = "https://www.boardgameatlas.com/api/reviews?&client_id=JLBr5npPhV" .
            "&description_required=true&order_by=date" .
            "&limit=$limit&game_id=" . $game->getGameId();
        // dd($url);
        $data = (new UploadDataService)
            ->uploadDataFromApi(
                $url,
                'reviews',
                '\reviews\game_' . $game->getGameId()
            );

        foreach ($data as $review) {
            $review->game = null;
            $review->user = null;
            $review->date = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $review->date);
            $review->rating = $review->rating > 0 && $limit % $review->rating != 1 ? $review->rating : null;
            // dd($review);
            $review->title = isset($review->title) && strlen($review->title) > 0
                ? $review->title
                : 'No title for review :' . $review->id
            ;

            $review->description = isset($review->description) &&  strlen($review->description) > 0
                ? $review->description
                : 'No description for review :' . $review->id
            ;


            dump(['review_'. $review->id=>$review]);

            $newReviews = new Review();
            foreach ($review as $key => $value) {
                $key = $key != 'id' ? $key != 'date' ? $key : 'created_At' : 'review_Id';
                $dynamicMethodName = "set" . str_replace(
                        ' ', '',
                        ucwords(
                            strtolower(
                                str_replace('_', ' ', $key)
                            )
                        )
                    );

                if (method_exists($newReviews, $dynamicMethodName) && $value) {

                    try {

                        $newReviews->$dynamicMethodName($value);
                    } catch (\Exception $exception) {
                        dump([
                            'Error message : ' => $exception->getMessage(),
                            'dynamicMethodName' => $dynamicMethodName,
                            'value' => $value
                        ]);
                    }
                }
            }

            $i = random_int(1, 29);
            $user = new User();
            try {
                $newReviews->setUser($this->getReference('User_' . $i));
            } catch (\Exception $e) {
                dump("Exeption add user " . $e->getMessage());
            }

            $newReviews->setGame($game);
            $newReviews->setValidated($i % 5 != 0);
            // dd(['newReviews'=>$newReviews]);
            $manager->persist($newReviews);
            $manager->persist($game);
            $manager->flush();
            // dd($newReviews);

        }

        // dump(['Reviews Fixtures' => Count($newData). " new Reviews in database"]);

    }




}
