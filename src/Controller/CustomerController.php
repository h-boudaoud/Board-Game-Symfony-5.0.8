<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\CustomerAddress;
use App\Entity\User;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/", name="customer_index", methods={"GET"})
     * @param CustomerRepository $customerRepository
     * @return Response
     */
    public function index(CustomerRepository $customerRepository): Response
    {
        return $this->render('customer/index.html.twig', [
            'customers' => $customerRepository->findAll(),
        ]);
    }

//    /**
//     * @Route("/new", name="customer_new", methods={"GET","POST"})
//     * @Route("/user/{id<\d+>}/new", name="customer_user_new", methods={"GET","POST"})
//     * @param Request $request
//     * @param User|null $user
//     * @return Response
//     */
//    public function new(Request $request, User $user=null): Response
//    {
//        $user = $user?$user:$this->getUser();
//        $customer = new Customer($user);
//        $form = $this->createForm(CustomerType::class, $customer);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($customer);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('customer_index');
//        }
//
//        return $this->render('customer/new.html.twig', [
//            'customer' => $customer,
//            'form' => $form->createView(),
//        ]);
//    }

    /**
     * @Route("/{id}", name="customer_show", methods={"GET"})
     * @param Customer $customer
     * @return Response
     */
    public function show(Customer $customer): Response
    {
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }


//    /**
//     * @Route("/{id}/edit", name="customer_edit", methods={"GET","POST"})
//     * @param Request $request
//     * @param Customer $customer
//     * @return Response
//     */
//    public function edit(Request $request, Customer $customer): Response
//    {
//        $form = $this->createForm(CustomerType::class, $customer);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('customer_index');
//        }
//
//        return $this->render('customer/edit.html.twig', [
//            'customer' => $customer,
//            'form' => $form->createView(),
//        ]);
//    }

    /**
     * @Route("/{id}", name="customer_delete", methods={"DELETE"})
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function delete(Request $request, Customer $customer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_index');
    }

    /**
     * @Route("/{id}", name="user_customer_mainAddress", methods={"POST"})
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function changMainAddress(Request $request, Customer $customer): Response
    {
        if ($this->isCsrfTokenValid('mainAddress'.$customer->getId(), $request->request->get('_token'))) {

            $id = $request->request->get('mainAddress');
            $address = $customer->getAddresses()->filter(function(CustomerAddress $a) use ($id)
                    {return $a->getId() == $id; }
                    )->first();
            if($address) {

                $entityManager = $this->getDoctrine()->getManager();
                $customer->setMainAddress($address);
                $entityManager->persist($customer);
                $entityManager->flush();
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

}
