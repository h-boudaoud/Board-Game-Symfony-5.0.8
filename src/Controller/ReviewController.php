<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/review")
 */
class ReviewController extends AbstractController
{
    /**
     * @Route("/", name="review_index", methods={"GET"})
     * @param ReviewRepository $reviewRepository
     * @return Response
     */
    public function index(ReviewRepository $reviewRepository): Response
    {
        $reviews=[];
        if (in_array('ROLE_MODERATOR', $this->getUser()->getRoles())) {
            $reviews = $reviewRepository->findAll();
        }else{
            $reviews = $reviewRepository->findBy(['user'=>$this->getUser()]);
        }
        return $this->render('review/index.html.twig', [
            'reviews' => $reviews,
            'title' => 'Reviews List',
        ]);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=401, message="No access! Get out!")
     * @Route("/user/{id<\d+>}-{username}", name="review_byUser", methods={"GET"})
     * @param ReviewRepository $reviewRepository
     * @param User $user
     * @return Response
     */
    public function reviewsByUser(ReviewRepository $reviewRepository,User $user): Response
    {
        if (!($this->getUser() && in_array('ROLE_MODERATOR', $this->getUser()->getRoles()))) {
            $user = $this->getUser();
        }
        //if ($this->getUser() && in_array('ROLE_MODERATOR', $this->getUser()->getRoles())) {
        //    $reviews = $user->getReviews();
        //}else{
        //    $reviews = $reviewRepository->findBy(['user'=>$this->getUser()]);
        //}
        return $this->render('review/index.html.twig', [
            'user'=>$user,
            'reviews' => $user->getReviews(),
            'title' => $user->getUserName().' user reviews list',
        ]);
    }

    /**
     * @Route("/game/{id<\d+>}-{name}", name="review_byGame", methods={"GET"})
     * @param ReviewRepository $reviewRepository
     * @param Game $game
     * @return Response
     */
    public function reviewsByGame(ReviewRepository $reviewRepository,?Game $game=null): Response
    {
        if(!$game){
        
            if (!($this->getUser() && in_array('ROLE_MODERATOR', $this->getUser()->getRoles()))) {
                $user =$this->getUser();
                return $this->render('review/index.html.twig', [
                    'user'=>$user,
                    'reviews' => $this->getUser()->getReviews(),
                    'title' => $user->getUserName().' user reviews list',
                ]);
            }
        
            return $this->render('review/index.html.twig', [
                'reviews' => $reviewRepository->findAll(),
                'title' => 'Reviews list',
            ]);
        }
        return $this->render('review/index.html.twig', [
            'reviews' => $reviewRepository->findBy(['game'=>$game]),
            'game' => $game,
            'title' => $game->getName().' game reviews list',
        ]);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=401, message="No access! Get out!")
     * @Route("/game/{id<\d+>}-{name}/new", name="review_new", methods={"GET","POST"})
     * @param Request $request
     * @param Game $game
     * @return Response
     */
    public function new(Request $request, Game $game): Response
    {
        // dd($game);
        $review = new Review();
        $review->setGame($game);
        $review->setUser($this->getUser());
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd(['review'=>$review, 'form'=>$form->get('addRating')->getData()]);
            if(!$form->get('addRating')->getData()) {
                $review->setRating(null);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();
            
        
            $this->addFlash(
                'success',
                'The request has been accepted'
            );
            
            return $this->redirectToRoute('review_byGame',[
                    'id'=>$game->getId(),
                    'name'=>$game->getName()
                ], 201);
        }
        
        if($form->isSubmitted()){
        
            $this->addFlash('error','The request not has been accepted');
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
            'game' => $game,
            'title' => 'New review',
        ]);
    }

//    /**
//     * @IsGranted("ROLE_MODERATOR", statusCode=401, message="No access! Get out!")
//     * @Route("/{id<\d+>}/edit", name="review_edit", methods={"GET","POST"})
//     * @param Request $request
//     * @param Review $review
//     * @return Response
//     */
//    public function edit(Request $request, Review $review): Response
//    {
//        $form = $this->createForm(ReviewType::class, $review);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('review_index');
//        }
//
//        return $this->render('review/edit.html.twig', [
//            'review' => $review,
//            'form' => $form->createView(),
//            'title' => 'Edit Review : game '.$review->getGame()->getName(),
//        ]);
//    }




    /**
     *
     * @IsGranted("ROLE_MODERATOR", statusCode=401, message="No access! Get out!")
     * @Route("/{id<\d+>}/delete", name="review_delete", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function delete(Request $request, Review $review): Response
    {
        $deleted = $review->getId() > 1296 ;
        if(
            $deleted &&
            (in_array('ROLE_MODERATOR', $this->getUser()->getRoles()) ||
                $review->getUser() === $this->getUser()
            ) &&
            $this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))
            ){
                // dump(['id'=>$review->getId()]);
                $entityManager = $this->getDoctrine()->getManager(); 
                $entityManager->persist($review);           
                // $review->setValidated(!$review->getValidated());
                $entityManager->remove($review);
                $entityManager->flush();
        }
        
        if($deleted){
        
            $this->addFlash(
                'success',
                '202 Accepted : The request was accepted'
            );
        }else{
            $this->addFlash(
                'warning',
                '403 Access forbidden: only the author of this solution who can delete or modify this entity <br /> Create a new entity to test this function.'
            );
        }
        

        return $this->redirect($request->headers->get('referer'));

    }
    
    /**
     * @IsGranted("ROLE_MODERATOR", statusCode=401, message="No access! Get out!")
     * @Route("/{id<\d+>}/validate", name="review_activate", methods={"POST"})
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function validateReview(Request $request, Review $review): Response
    {
        $updated = $review->getId() > 1296 ;

        if (
            $updated &&
            $this->isCsrfTokenValid('validate' . $review->getId(), $request->request->get('_token'))
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $review->setValidated(true);
            $entityManager->persist($review);
            $entityManager->flush();
        }
        
        if($updated){
        
            $this->addFlash(
                'success',
                '202 Accepted : The request was accepted'
            );
        }else{
            $this->addFlash(
                'warning',
                '403 Access forbidden: only the author of this solution who can delete or modify this entity <br /> Create a new entity to test this function.'
            );
        }
        return $this->redirect($request->headers->get('referer'));
    }
    

    /**
     * @Route("/{id<\d+>}", name="review_show", methods={"GET"})
     * @param Review $review
     * @return Response
     */
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
            'title' => 'Show review : game '.$review->getGame()->getName(),
        ]);
    }
}
