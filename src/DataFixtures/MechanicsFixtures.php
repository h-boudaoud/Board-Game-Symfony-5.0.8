<?php

namespace App\DataFixtures;

use App\Entity\Mechanic;
use App\Service\FixturesUploadDataService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

// use Doctrine\Common\Persistence\ObjectManager;

class MechanicsFixtures extends Fixture
{
    private $data;
    /**
     * @var FixturesUploadDataService
     */
    private $service;

    public function __construct(FixturesUploadDataService $dataService)
    {
        $url = 'https://www.boardgameatla.com/api/game/mechanics?client_id=JLBr5npPhV';
        $this->data = $dataService->uploadDataFromApi($url, 'mechanics');
        $this->service = $dataService;
    }

    public function load(ObjectManager $manager)
    {
        // dd(Count($this->gamesData));
        $newData=[];

        foreach ($this->data as $mechanic) {
            $newMechanic = new Mechanic();
            $mechanicId =$mechanic->id;
            $mechanic->name= strlen($mechanic->name)>0
                ? $mechanic->name
                : "$mechanic->id. mechanic"
            ;
            $this->service->dataFormatting($mechanic,$newMechanic,'mechanic');

            $manager->persist($newMechanic);
            $manager->flush();
            $this->addReference("Mechanic_$mechanicId",(object)$newMechanic);
            // dump(["Mechanic_$mechanicId"=>$this->getReference("Mechanic_$mechanicId")]);
            $newData[$newMechanic->getMechanicId()] = $newMechanic;
        }

        dump(['Mechanics Fixtures' => Count($newData). " new Mechanics in database"]);

    }
}
