<?php

namespace App\Controller;

use App\Entity\Mechanic;
use App\Form\MechanicType;
use App\Repository\MechanicRepository;
use App\Service\IsAuthorized;
use Doctrine\Persistence\ObjectManager;
use Exception;
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
     * Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
     * This is the number of objects persisted in the database with the fixed data.
     *
     * @var integer
     */
    private const NB_INITIAL_IN_DATABASE = 122;

    /**
     * @var MechanicRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(MechanicRepository $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }


    /**
     * @Route("/", name="mechanic_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('mechanic/index.html.twig', [
            'mechanics' => $this->repository->findAll(),
            //'mechanics' => null,
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
            'title' => 'Games ' . $mechanic->getName(),
        ]);
    }

    /**
     * @IsGranted("ROLE_STOREKEEPER", statusCode=401, message="No access! Get out!")
     * @Route("/new", name="mechanic_new", methods={"GET","POST"})
     * @Route("/{id<\d+>}-{name}/edit", name="mechanic_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Mechanic $mechanic
     * @return Response
     */
    public function post(Request $request, Mechanic $mechanic=null): Response
    {
        if(!$mechanic){
            $mechanic = new Mechanic();
            $isAuthorized =[];
        }else {
            //Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
            $isAuthorized = IsAuthorized::ToModifyEntity($mechanic->getId(), 'mechanic');
        }
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } else {
            $form = $this->createForm(MechanicType::class, $mechanic);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->manager->persist($mechanic);
                    $this->manager->flush();

                    $this->addFlash(
                        'success',
                        '202 Accepted : The request was accepted'
                    );

                    return $this->redirectToRoute('mechanic_index');
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

        //Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
        $isAuthorized = IsAuthorized::ToModifyEntity($mechanic->getId(), 'mechanic');
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } else {
            if ($this->isCsrfTokenValid('delete' . $mechanic->getId(), $request->request->get('_token'))) {

                $this->manager->remove($mechanic);
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
