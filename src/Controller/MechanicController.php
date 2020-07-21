<?php

namespace App\Controller;

use App\Entity\Mechanic;
use App\Form\MechanicType;
use App\Repository\MechanicRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mechanic")
 */
class MechanicController extends AbstractController
{
    /**
     * @Route("/", name="mechanic_index", methods={"GET"})
     * @param MechanicRepository $mechanicRepository
     * @return Response
     */
    public function index(MechanicRepository $mechanicRepository): Response
    {
        return $this->render('mechanic/index.html.twig', [
            'mechanics' => $mechanicRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     * @Route("/new", name="mechanic_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $mechanic = new Mechanic();
        $form = $this->createForm(MechanicType::class, $mechanic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mechanic);
            $entityManager->flush();
            
            $this->addFlash(
                        'success',
                        '202 Accepted : The request was accepted'
                    );

            return $this->redirectToRoute('mechanic_index');
        }

        return $this->render('mechanic/new.html.twig', [
            'mechanic' => $mechanic,
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @Route("/{id<\d+>}-{name}", name="mechanic_show", methods={"GET"})
     * @param Mechanic $mechanic
     * @return Response
     */
    public function show(Mechanic $mechanic): Response
    {
        if ($this->getUser() && in_array('ROLE_STOREKEEPER', $this->getUser()->getRoles())) {
            return $this->render('mechanic/show.html.twig', [
                'mechanic' => $mechanic,
            ]);
        }

        return $this->render('game/index.html.twig', [
            'games' => $mechanic->getGames(),
            'title' => 'Games '.$mechanic->getName(),
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     * @Route("/{id<\d+>}-{name}/edit", name="mechanic_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Mechanic $mechanic
     * @return Response
     */
    public function edit(Request $request, Mechanic $mechanic): Response
    {
        $form = $this->createForm(MechanicType::class, $mechanic);
        if($mechanic->getId()<119){
            $this->addFlash(
                'warning',
                '403 Access forbidden: only the author of this solution who can delete or modify this entity <br /> Create a new entity to test this function.'
            );            
        }else{
                $form = $this->createForm(MechanicType::class, $mechanic);
                $form->handleRequest($request);
        
                if ($form->isSubmitted() && $form->isValid()) {
                    $this->getDoctrine()->getManager()->flush();
                    
                    $this->addFlash(
                        'success',
                        '202 Accepted : The request was accepted'
                    );
        
                    return $this->redirectToRoute('mechanic_index');
                }
        }
        return $this->render('mechanic/edit.html.twig', [
            'mechanic' => $mechanic,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     * @Route("/{id<\d+>}/delete", name="mechanic_delete", methods={"DELETE"})
     * @param Request $request
     * @param Mechanic $mechanic
     * @return Response
     */
    public function delete(Request $request, Mechanic $mechanic): Response
    {
        if($mechanic->getId()<119){
            $this->addFlash(
                'warning',
                '403 Access forbidden: only the author of this solution who can delete or modify this entity <br /> Create a new entity to test this function.'
            );            
        }else{
            if ($this->isCsrfTokenValid('delete'.$mechanic->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($mechanic);
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
