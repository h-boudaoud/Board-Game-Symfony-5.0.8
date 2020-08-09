<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\ChangePasswordUserType;
use App\Form\User\UserProfileEditType;
use App\Form\User\RegisterUserType;
use App\Form\User\RoleUserType;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Service\IsAuthorized;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function MongoDB\BSON\fromJSON;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $encoder;
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        ObjectManager $manager,
        UserRepository $repository
    )
    {
        $this->encoder = $encoder;
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * @IsGranted("ROLE_USER_MANAGER", statusCode=401, message="No access! Get out!")
     *
     * @Route("/", name="user_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $this->repository->findBy([], ['userName' => 'ASC']),
            'title' => 'Users'
        ]);
    }

    /**
     *
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->encodePassword($this->encoder);
            // dd($user);
            $this->manager->persist($user);
            $this->manager->flush();
            $path = $this->getUser() && in_array('ROLE_USER_MANAGER', $this->getUser()->getRoles())
                ? 'user_index'
                : 'login';

            $this->addFlash(
                'success',
                'The request has been accepted'
            );

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
     * @IsGranted("ROLE_USER_MANAGER", statusCode=401, message="No access! Get out!")     *
     * @Route("/admin/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function adminShow(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'title' => 'User infos'
        ]);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=401, message="No access! Get out!")     *
     * @Route("/profile/", name="user_profile", methods={"GET"})
     * @return Response
     */
    public function showUserProfile(): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $this->getUser(),
            'title' => 'User info'
        ]);
    }


    /**
     * @IsGranted("ROLE_USER", statusCode=401, message="No access! Get out!")
     * @Route("/profile/password", name="user_profile_password", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function userPasswordEdit(Request $request): Response
    {

        $user = $this->getUser();

        //The first if is only for the demo solution. Removed for product solution
        // To Do not change initial objects in database
        $isAuthorized = IsAuthorized::ToModifyEntity($user->getId(), 'user');

        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } else {
            $userPassword = $user->getPassword();

            $form = $this->createForm(ChangePasswordUserType::class, $user);
            $form->handleRequest($request);

            return $this->edit(
                $user, $form, 'html', $form->get('userPassword')->getData() &&
                password_verify($form->get('userPassword')->getData(), $userPassword)
            );
        }

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
        $typeResponse = strstr($request->headers->get('accept'), 'json') ? 'json' : 'html';
//        dd([
//            $typeResponse,
//            $request->headers->get('accept'),
//            $request->headers->get('referer'),
//            $request->server,
//            $request,
//            $this->getUser()
//        ]);

        $user = $this->getUser();


        //The first if is only for the demo solution. Removed for product solution
        // To Do not change initial objects in database
        $isAuthorized = IsAuthorized::ToModifyEntity($user->getId(), 'user');
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } else {
            $form = $this->createForm(UserProfileEditType::class, $user);
            $form->handleRequest($request);

            return $this->edit(
                $user, $form, $typeResponse, password_verify($form->get('userPassword')->getData(), $user->getPassword())
            );
        }

    }


    /**
     *
     * IsGranted("ROLE_USER_MANAGER", statusCode=401, message="No access! Get out!")
     *
     * @Route("/admin/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function adminEdit(Request $request, User $user): Response
    {
        $typeResponse = strstr($request->headers->get('accept'), 'json') ? 'json' : 'html';
        //dd(['adminAuthorized'=>$this->adminAuthorized($user, $request)]);
        //dd($user);

        //The first if is only for the demo solution. Removed for product solution
        // To Do not change initial objects in database
        $isAuthorized = IsAuthorized::ToModifyEntity($user->getId(), 'user');
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } else
            if (!$this->ToModifyUser($user)) {
                return $this->redirectToRoute('user_index');
            } else {

                $form = $this->createForm(RoleUserType::class, $user);
                $form->handleRequest($request);


                return $this->edit($user, $form, $typeResponse, $this->isGranted('ROLE_USER_MANAGER'));
            }
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
        $user = $this->getUser();
        //The first if is only for the demo solution. Removed for product solution
        // To Do not change initial objects in database
        $isAuthorized = IsAuthorized::ToModifyEntity($user->getId(), 'user');
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } else {
            try {
                // dump(['userDelete' => $user]);
                $this->delete($request, $user);

                $this->addFlash(
                    'success',
                    '202 Accepted : The request was accepted'
                );
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    '400 Bad Request : The server was unable to process the request due to incorrect syntax.' .
                    '<br />Message the administrator if the problem persists.' .
                    (in_array("ROLE_USER_MANAGER", $this->getUser()->getRoles()))
                        ? '<br />' . $e->getMessage() : ''
                );
                return $this->redirect($request->headers->get('referer'));
            }

            return $this->redirectToRoute('logout', [], 301);
        }

    }

    /**
     *
     * @IsGranted("ROLE_USER_MANAGER", statusCode=401, message="No access! Get out!")
     *
     * @Route("/admin/{id}/delete", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function adminDelete(Request $request, User $user): Response
    {
        //The first if is only for the demo solution. Removed for product solution
        // To Do not change initial objects in database
        $isAuthorized = IsAuthorized::ToModifyEntity($user->getId(), 'user');
        if (Count($isAuthorized)) {
            $this->addFlash($isAuthorized['type'], $isAuthorized['message']);
            return $this->redirect($request->headers->get('referer'));
        } elseif (!$this->ToModifyUser($user)) {

            return $this->redirect($request->headers->get('referer'));
        } else {
            $this->delete($request, $user);
            return $this->redirectToRoute('user_index');
        }

    }




    /********************************/
    /*       Privates functions     */
    /********************************/

    /**
     * @param User $user
     * @param $form
     * @param bool $isAuthorized
     * @param string $typeResponse
     * @return Response
     */
    private function edit(User $user, $form, ?string $typeResponse = 'html', ?bool $isAuthorized = false): Response
    {
        //dd($form->isSubmitted() && $form->isValid()) ;

        if ($form->isSubmitted() && $form->isValid()) {
            $path = $this->getUser() == $user ? 'user_profile' : 'user_index';
            $type = 'error';
            $message = '401 Access unauthorized : You don\'t authorized to perform this operation.';
            if ($isAuthorized) {

                $type = 'success';
                $message = '202 Accepted : The request was accepted';
                if ($form->has('newPassword')) {
                    $user->encodePassword($this->encoder);
                    $path = 'logout';
                }

                $customer = $form->has('customer') ? $form->get('customer')->getData() : null;
                if ($customer && $customer->getTitle()) {
                    $customer = $user->getCustomer();

                    if ($form->get('customer')->get('newAddress')->getData()) {
                        $isMainAddress = $form->get('customer')->get('addresses')->get('isMainAddress')->getData();
                        $newAddress = $form->get('customer')->get('addresses')->getData();
                        $this->manager->persist($newAddress);

                        if ($isMainAddress) {
                            $customer->setMainAddress($newAddress);
                        } else {
                            $customer->addAddress($newAddress);
                        }
                    }

                    $this->manager->persist($customer);
                } else {
                    $user->setCustomer(null);
                }
                //dd($user);
                $this->manager->persist($user);
                $this->manager->flush();

            }

            if ($typeResponse == 'json') {
                $addresses = [];
                foreach ($user->getcustomer()->getAddresses() as $address) {
                    $addresses[] = [
                        'id' => $address->getId(),
                        'mainAddress' => $address == $user->getcustomer()->getMainAddress(),
                        'address' => $address->toString()
                    ];
                }
                $status = $type == 'success' ? 200 : 400;
                return $this->json(['data' => [
                    'message' => $message,
                    'code' => $status,
                    'addresses' => $addresses
                ]], $status);
            }
            $this->addFlash($type, $message);
            return $this->redirectToRoute($path);
        }

        if ($typeResponse == 'json') {
            $response = $this->render('user/_form.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'title' => 'Update user infos'
            ]);
            return $this->json(['data' => $response->getContent(), 'user' => $user], 200);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'title' => 'Update user infos'
        ]);
    }


    private function delete(Request $request, User $user)
    {
        if (
        $this->isCsrfTokenValid(
            'delete' . $user->getId(),
            $request->request->get('_token')
        )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($user->getCustomer()) {
                $customer = $user->getCustomer()->setUser(null);
                $entityManager->persist($customer);
            }
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                '202 Accepted : The request was accepted'
            );
        } else {
            $this->addFlash(
                'error',
                '401 Access unauthorized : You don\'t authorized to perform this operation.'
            );
            return $this->redirect($request->headers->get('referer'));
        }
    }


    /**
     * To verify user roles before Update or delete any user.
     * @param User $user
     * @return bool
     */
    private function ToModifyUser(User $user)
    {

        // dd([$this->getUser(), $user]);
//        dd (
//            $this->isGranted('ROLE_SUPER_ADMIN') ||
//            (
//                in_array('ROLE_USER_MANAGER', $this->getUser()->getRoles()) &&
//                !in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles()) &&
//                !in_array('ROLE_SUPER_ADMIN', $user->getRoles())
//            )
//        );

        if (
            $this->getUser() && $user &&
            (
                $this->isGranted('ROLE_SUPER_ADMIN') ||
                (
                    in_array('ROLE_ADMIN', $this->getUser()->getRoles()) &&
                    !in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles()) &&
                    !in_array('ROLE_SUPER_ADMIN', $user->getRoles())
                ) ||
                (
                    in_array('ROLE_USER_MANAGER', $this->getUser()->getRoles()) &&
                    !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) &&
                    !in_array('ROLE_ADMIN', $user->getRoles())
                )
            )
        ) {
            return true;
        }

        $this->addFlash(
            'error',
            '401 Access unauthorized : You don\'t authorized to perform this operation.'
        );

        return false;
    }


}
