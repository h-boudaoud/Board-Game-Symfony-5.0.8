<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use App\Service\IsAuthorized;
use Doctrine\Persistence\ObjectManager;
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
     * Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
     * This is the number of objects persisted in the database with the fixed data.
     */
    private const NB_INITIAL_IN_DATABASE =  1619;

    /**
     * @var ReviewRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ReviewRepository $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @IsGranted("ROLE_MODERATOR", statusCode=401, message="No access! Get out!")
     * @Route("/admin", name="review_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('review/index.html.twig', [
            'reviews' => $this->repository->findBy([], ['createdAt' => 'DESC']),
            'title' => 'Reviews List',
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR", statusCode=401, message="No access! Get out!")
     * @Route("/admin/new_reviews", name="review_newReviews", methods={"GET"})
     * @return Response
     */
    public function newReviews(): Response
    {
        return $this->render('review/index.html.twig', [
            'reviews' => $this->repository->findBy(['validated' => false], ["createdAt" => "ASC"]),
            //'reviews' => $this->repository->findAll(),
            'title' => 'Reviews List',
        ]);
    }

    /**
     * IsGranted("ROLE_USER", statusCode=401, message="No access! Get out!")
     * @Route("/user/{id<\d+>}-{username}", name="review_byUser", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function reviewsByUser(User $user): Response
    {
        return $this->render('review/index.html.twig', [
            'user' => $user,
            'reviews' => $user->getReviews(),
            'title' => $user->getUserName() . ' user reviews list',
        ]);
    }

    /**
     * @Route("/game/{id<\d+>}-{name}", name="review_byGame", methods={"GET"})
     * @param Game $game
     * @return Response
     */
    public function reviewsByGame(Game $game): Response
    {

        return $this->render('review/index.html.twig', [
            'reviews' => $game->getReviews(),
            //'reviews' => $this->repository->findBy(['game' => $game, ['createdAt' => 'DESC']]),
            'game' => $game,
            'title' => $game->getName() . ' game reviews list',
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
            try{
                if (!$form->get('addRating')->getData()) {
                    $review->setRating(null);
                }

                $this->manager->persist($review);
                $this->manager->flush();

                $this->addFlash(
                    'success',
                    'The request has been accepted'
                );

                return $this->redirectToRoute('review_byGame', [
                    'id' => $game->getId(),
                    'name' => $game->getName()
                ], 201);
            }catch(\Exception $e){
                $this->addFlash(
                    'error',
                    '400 Bad Request : The server was unable to process the request due to incorrect syntax.' .
                    '<br />Message the administrator if the problem persists.'.
                    (in_array("ROLE_STOREKEEPER", $this->getUser()->getRoles()))
                        ?'<br />'.$e->getMessage():''
                );
            }
        }

        if ($form->isSubmitted()) {

            $this->addFlash('error', 'The request not has been accepted');
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
//            $review->setValidated(false);
//            $this->manager->persist($review);
//            $this->manager->flush();
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
     * @IsGranted("ROLE_USER", statusCode=401, message="No access! Get out!")
     * @Route("/{id<\d+>}/delete", name="review_delete", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function delete(Request $request, Review $review): Response
    {

        //Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
        $isAuthorized = IsAuthorized::ToModifyEntity($review->getId(), 'review');
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } else if (
            (in_array('ROLE_MODERATOR', $this->getUser()->getRoles()) ||
                $review->getUser() === $this->getUser()
            ) &&
            $this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))
        ) {
            // dump(['id'=>$review->getId()]);
            $this->manager->persist($review);
            // $review->setValidated(!$review->getValidated());
            $this->manager->remove($review);
            $this->manager->flush();

            $this->addFlash(
                'success',
                '202 Accepted : The request was accepted '
            );
        } else {
            $this->addFlash(
                'error',
                '401 Access unauthorized : You don\'t authorized to perform this operation.'
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
        //Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
        $isAuthorized = IsAuthorized::ToModifyEntity($review->getId(), 'review');
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
        } else if (
            $this->isCsrfTokenValid('validate' . $review->getId(), $request->request->get('_token'))
        ) {
            $review->setValidated(!$review->getValidated());
            $this->manager->persist($review);
            $this->manager->flush();

            $this->addFlash(
                'success',
                '202 Accepted : The request was accepted '
            );
        } else {
            $this->addFlash(
                'error',
                '401 Access unauthorized : You don\'t authorized to perform this operation.'
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
            'title' => 'Show review : game ' . $review->getGame()->getName(),
        ]);
    }
}
