<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\IsAuthorized;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{

    /**
     * Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
     * This is the number of objects persisted in the database with the fixed data.
     *
     * @var integer
     */
    private const NB_INITIAL_IN_DATABASE = 122;

    /**
     * @var CategoryRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(CategoryRepository $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }


    /**
     * @Route("/", name="category_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $this->repository->findAll(),
            //'categories' => null,
        ]);
    }

    /**
     *
     * @Route("/{id<\d+>}-{name}", name="category_show", methods={"GET"})
     * @param Category $category
     * @return Response
     */
    public function show(Category $category): Response
    {
        if ($this->getUser() && in_array('ROLE_STOREKEEPER', $this->getUser()->getRoles())) {
            return $this->render('category/show.html.twig', [
                'category' => $category,
            ]);
        }

        return $this->render('game/index.html.twig', [
            'games' => $category->getGames(),
            'title' => 'Games ' . $category->getName(),
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     * @Route("/new", name="category_new", methods={"GET","POST"})
     * @Route("/{id<\d+>}-{name}/edit", name="category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function post(Request $request, Category $category=null): Response
    {
        if(!$category){
            $category = new Category();
            $isAuthorized =[];
        }else {
            //Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
            $isAuthorized = IsAuthorized::ToModifyEntity($category->getId(), 'category');
        }
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } else {
            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->manager->persist($category);
                    $this->manager->flush();

                    $this->addFlash(
                        'success',
                        '202 Accepted : The request was accepted'
                    );

                    return $this->redirectToRoute('category_index');
                } catch (Exception $e) {
                    $this->addFlash(
                        'error',
                        '400 Bad Request : The server was unable to process the request due to incorrect syntax.' .
                        '<br />Message the administrator if the problem persists.'.
                        (in_array("ROLE_STOREKEEPER", $this->getUser()->getRoles()))
                            ?'<br />'.$e->getMessage():''
                    );
                }
            }
        }
        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     * @Route("/{id<\d+>}/delete", name="category_delete", methods={"DELETE"})
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function delete(Request $request, Category $category): Response
    {

        //Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
        $isAuthorized = IsAuthorized::ToModifyEntity($category->getId(), 'category');
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } else {
            if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {

                $this->manager->remove($category);
                $this->manager->flush();
                $this->addFlash(
                    'success',
                    '202 Accepted : The request was accepted'
                );
            } else {
                $this->addFlash(
                    'error',
                    '401 Access unauthorized : You don\'t authorized to perform this operation.'
                );
            }

        }

        return $this->redirect($request->headers->get('referer'));
    }
}
