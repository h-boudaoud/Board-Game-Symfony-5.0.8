<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Service\UploadDataService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

// use Doctrine\Common\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    private $data;
    public const CATEGORIES_REFERENCE = 'categoriesFixtures';

    public function __construct(UploadDataService $dataService)
    {
        $url = 'https://www.boardgameatlas.com/api/game/categories?client_id=JLBr5npPhV';
        $this->data = $dataService->uploadDataFromApi($url, 'categories');
    }

    public function load(ObjectManager $manager)
    {
        dump('Category Fixtures : ' .Count($this->data));
        $newData=[];

        foreach ($this->data as $category) {
            $new = new Category();
    
            foreach ($category as $key => $value) {
                $key = $key != 'id' ? $key : 'category_Id';
                $dynamicMethodName = "set" . str_replace(
                        ' ', '',
                        ucwords(
                            strtolower(
                                str_replace('_', ' ', $key)
                            )
                        )
                    );

                if ($key == "name" && empty($value)) {
                    $value = (Count($category->names)>0)?$category->names[0]:$category->id;
                    $category->name=$value;
                }

                if (method_exists($new, $dynamicMethodName) && $value) {

                    try {
                        $new->$dynamicMethodName($value);
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

            $manager->persist($new);
            $manager->flush();
            $this->addReference('Category_'.$category->id,(object)$new );
            // dump([$newGame->getId()=>$newGame->getGameId().' - '.$newGame->getName()]);
            $newData[$new->getCategoryId()] = $new;
            // dd($newData);
        }

        dump(['Categories Fixtures' => Count($newData). " new Categories in database"]);

    }


}
