<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('security/index.html.twig', [
            'title' => 'Welcome',
        ]);
    }


    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $lastUserName = $authenticationUtils->getLastUsername();
        //dd($this->getDoctrine()->getRepository(User::class)->findBy(['userName'=>$lastUserName]));
        $error = $authenticationUtils->getLastAuthenticationError();
//        dump($request->getSession());
        return $this->render('security/login.html.twig', [
            'title' => 'Login',
            'lastUserName' => $lastUserName,
            'error' => $error,
            'route' => 'login'
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public static function logout()
    {
    }



}
