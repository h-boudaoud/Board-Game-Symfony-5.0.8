<?php

namespace App\DataFixtures;

use App\Entity\Mechanic;
use App\Service\UploadDataService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

// use Doctrine\Common\Persistence\ObjectManager;

class MechanicsFixtures extends Fixture
{
    private $data;

    public function __construct(UploadDataService $dataService)
    {
        $url = 'https://www.boardgameatlas.com/api/game/mechanics?client_id=JLBr5npPhV';
        $this->data = $dataService->uploadDataFromApi($url, 'mechanics');
    }

    public function load(ObjectManager $manager)
    {
        // dd(Count($this->gamesData));
        $newData=[];

        foreach ($this->data as $mechanic) {
            $new = new Mechanic();
            $mechanicId =$mechanic->id;

            foreach ($mechanic as $key => $value) {
                $key = $key != 'id' ? $key : 'mechanic_Id';
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
                    $value = (Count($mechanic->names)>0)?$mechanic->names[0]:$mechanic->id;
                    $mechanic->name=$value;
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


//                    if($key === 'mechanic_Id') {
//                        dd([
//                            'dynamicMethodName' => $dynamicMethodName,
//                            'value' => $value,
//                            'method_exists' => method_exists($new, $dynamicMethodName)
//                        ]);
//                    }

                }

            }

            $manager->persist($new);
            $manager->flush();
            $this->addReference("Mechanic_$mechanicId",(object)$new);
            dump(["Mechanic_$mechanicId"=>$this->getReference("Mechanic_$mechanicId")]);
            $newData[$new->getMechanicId()] = $new;
        }

        dump(['Mechanics Fixtures' => Count($newData). " new Mechanics in database"]);

    }
}
