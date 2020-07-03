<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\RegisterUserType;
use App\Form\User\RoleUserType;
use App\Form\User\UserInfoType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=401, message="No access! Get out!")
     *
     * @Route("/", name="user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'title' => 'Users'
        ]);
    }

    /**
     *
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param User|null $user
     * @return Response
     */
    public function new(
        UserPasswordEncoderInterface $encoder,
        Request $request, User $user = null

    ): Response
    {

        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->encodePassword($encoder);
            // dd($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $path = $this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())
                ?'user_index'
                :'login'
            ;
            return $this->redirectToRoute($path);
//            return $this->render('security/login.html.twig', [
//                'lastUserName' => $user->getUserName(),
//                'title' => 'User',
//                'route' => 'login'
//            ]);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'title' => 'New user'
        ]);
    }

    /**
     *
     * @IsGranted("ROLE_ADMIN", statusCode=401, message="No access! Get out!")
     *
     * @Route("/admin/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'title' => 'User infos'
        ]);
    }

    /**
     *
     * @IsGranted("ROLE_ADMIN", statusCode=401, message="No access! Get out!")
     *
     * @Route("/admin/{id}/delete", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function adminDelete(Request $request, User $user): Response
    {
        $this->delete($request, $user);
        return $this->redirectToRoute('user_index');
    }


    /**
     *
     * @IsGranted("ROLE_USER", statusCode=401, message="No access! Get out!")
     *
     * @Route("/profile/", name="user_profile", methods={"GET"})
     * @return Response
     */
    public function profil(): Response
    {
        // dd($this->getUser());
        return $this->render('user/show.html.twig', [
            'user' => $this->getUser(),
            'title' => 'User info'
        ]);
    }

    /**
     *
     * @IsGranted("ROLE_USER", statusCode=401, message="No access! Get out!")
     *
     * @Route("/profile/edit", name="user_profile_edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function userEdit(Request $request): Response
    {
        $user = $this->getUser() ;
        $form = $this->createForm(UserInfoType::class, $user);
        return $this->edit($request, $user, $form);

    }


    /**
     *
     * @IsGranted("ROLE_ADMIN", statusCode=401, message="No access! Get out!")
     *
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function adminEdit(Request $request, User $user): Response
    {
//        $form = null;
//        if (!$this->getUser()) {
//            return $this->redirectToRoute('game_index', [], 301);
//        } elseif ($user != $this->getUser() && $this->getUser()->getRoles() &&
//            (
//                in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ||
//                in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles())
//            )
//        ) {
            $form = $this->createForm(RoleUserType::class, $user);

        return $this->edit($request, $user, $form);
    }


    /**
     *
     * @IsGranted("ROLE_USER", statusCode=401, message="No access! Get out!")
     *
     * @Route("/profile/delete", name="user_profile_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @return Response
     */
    public function userDelete(Request $request): Response
    {
        $this->delete($request, $this->getUser());
        return $this->redirectToRoute('logout', [], 301);

    }






    /********************************/
    /*       Privates functions     */
    /********************************/

    private function edit(Request $request, User $user, $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $path = $this->getUser()==$user?'user_profile':'user_index';
            return $this->redirectToRoute($path);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'title' => 'Update user infos'
        ]);
    }


    private function delete(Request $request, User $user)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
    }


}
