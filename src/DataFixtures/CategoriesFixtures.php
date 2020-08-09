<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Service\FixturesUploadDataService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

// use Doctrine\Common\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    private $data;
    public const CATEGORIES_REFERENCE = 'categoriesFixtures';
    private $service;

    public function __construct(FixturesUploadDataService $dataService)
    {
        $url = 'https://www.boardgameatla.com/api/game/categories?client_id=JLBr5npPhV';
        $this->data = $dataService->uploadDataFromApi($url, 'categories');
        $this->service = $dataService;
    }

    public function load(ObjectManager $manager)
    {
        dump('CategoriesFixtures : ' . Count($this->data));
        $newCategories=[];

        foreach ($this->data as $category) {
            $newCategory = new Category();
            $categoryId =$category->id;
            $category->name= strlen($category->name)>0 ? $category->name : "$category->id. category";


            // dd(['array'=> $category,'is_array'=> is_array($category), 'Object' => $newCategory, 'string'=> 'category']);
            $this->service->dataFormatting($category,$newCategory,'category');

            $manager->persist($newCategory);
            $manager->flush();
            $this->addReference("Category_$categoryId",(object)$newCategory);
            // dump(["Category_$categoryId"=>$this->getReference("Category_$categoryId")]);
            $newCategories[$newCategory->getCategoryId()] = $newCategory;
        }

        dump(['Categorys Fixtures' => Count($newCategories). " new Categorys in database"]);

    }

}
