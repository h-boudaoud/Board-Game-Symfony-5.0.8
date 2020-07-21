<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
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
     * @Route("/", name="category_index", methods={"GET"})
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     * @Route("/new", name="category_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash(
                'success',
                '202 Accepted : The request was accepted'
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
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
            'title' => 'Games '.$category->getName(),
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     * @Route("/{id<\d+>}-{name}/edit", name="category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        if($category->getId()<123){
            $this->addFlash(
                'warning',
                '403 Access forbidden: only the author of this solution who can delete or modify this entity <br /> Create a new entity to test this function.'
            );
        }else{
            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash(
                    'success',
                    '202 Accepted : The request was accepted'
                );

                return $this->redirectToRoute('category_index');
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
        if($category->getId()<123){
            $this->addFlash(
                'warning',
                '403 Access forbidden: only the author of this solution who can delete or modify this entity <br /> Create a new entity to test this function.'
            );
        }else{
            if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($category);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    '202 Accepted : The request was accepted'
                );
            }else{
                $this->addFlash(
                    'error',
                    '401 Access unauthorized : You don\'t authorized to perform this operation.'
                );
            }

        }

        return $this->redirect($request->headers->get('referer'));
    }
}
