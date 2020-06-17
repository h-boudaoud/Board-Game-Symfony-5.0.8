<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Form\GameType;
use App\Repository\GameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/game")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/", name="game_index", methods={"GET"})
     * @param GameRepository $gameRepository
     * @return Response
     */
    public function index(GameRepository $gameRepository): Response
    {
        $games = null;
        if (
            $this->getUser() &&
            (
                in_array('ROLE_STOREKEEPER', $this->getUser()->getRoles()) ||
                in_array('ROLE_ADMIN', $this->getUser()->getRoles())
            )
        ) {
            $games = $gameRepository->findAll();
        } else {
            $games = $gameRepository->findBy(['published' => true]);
        }
        // dd($games);
        return $this->render('game/index.html.twig', [
            'games' => $games,
            'title' => 'Games List',
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     * @Route("/new", name="game_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game_index');
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
            'title' => 'Game',
        ]);
    }

    /**
     * @Route("/{id}", name="game_show", methods={"GET"})
     * @param Game $game
     * @return Response
     */
    public function show(Game $game): Response
    {
        return $this->render('game/show.html.twig', [
            'game' => $game,
            'title' => 'Game infos',
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     *
     * @Route("/{id}/edit", name="game_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Game $game
     * @return Response
     */
    public function edit(Request $request, Game $game): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_index');
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
            'title' => 'Game',
        ]);
    }

    /**
     *
     * Token in form : IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     *
     * @Route("/{id}", name="game_delete", methods={"DELETE"})
     * @param Request $request
     * @param Game $game
     * @return Response
     */
    public function delete(Request $request, Game $game): Response
    {
        $userRoles = [];
        // To check an token in the request matched a user role
//        foreach ($request->request->get('roles') as $tokenRole) {
//            foreach (User::ROLES as $role) {
//                if ($this->isCsrfTokenValid($role, $tokenRole)) {
//                    $userRoles[] = $role;
//                    break;
//                }
//            }
//        }
        foreach ($request->request->get('roles') as $tokenRole) {
            if ($this->isCsrfTokenValid('ROLE_USER', $tokenRole)) {
                $userRoles[] = 'ROLE_USER';
            }if ($this->isCsrfTokenValid('ROLE_STOREKEEPER', $tokenRole)) {
                $userRoles[] = 'ROLE_STOREKEEPER';
            }
        }

        if (
            $this->isCsrfTokenValid('delete' . $game->getId(), $request->request->get('_token')) &&
//            (in_array('ROLE_USER', $userRoles) || in_array('ROLE_STOREKEEPER', $userRoles))
            Count($userRoles) > 0
        ) {

            $entityManager = $this->getDoctrine()->getManager();
            $game->setPublished(false);
            if (in_array('ROLE_ADMIN', $userRoles)) {
                $entityManager->remove($game);
            } elseif (in_array('ROLE_STOREKEEPER', $userRoles)) {
                $entityManager->persist($game);
            }

            $entityManager->flush();

        }
        return $this->redirectToRoute('game_index');
    }
}
