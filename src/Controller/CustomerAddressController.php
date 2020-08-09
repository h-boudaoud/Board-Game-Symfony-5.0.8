<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\CustomerAddress;
use App\Entity\User;
use App\Form\CustomerAddressType;
use App\Repository\CustomerAddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer/address")
 */
class CustomerAddressController extends AbstractController
{
    /**
     * @Route("/", name="customer_address_index", methods={"GET"})
     * @param CustomerAddressRepository $customerAddressRepository
     * @return Response
     */
    public function index(CustomerAddressRepository $customerAddressRepository): Response
    {
        return $this->render('customer_address/index.html.twig', [
            'customer_addresses' => $customerAddressRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="customer_address_new", methods={"GET","POST"})
     * @Route("/{id<\d+>}-{userName}/new", name="customer_user_address_new", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function new(Request $request, User $user = null): Response
    {
        $typeResponse = strstr($request->headers->get('accept'), 'json') ? 'json' : 'html';
        if (!$user || !$user->getCustomer()) {
            $user = $this->getUser();
        }
        $customer = $user->getCustomer();

        if (!$customer) {
            $customer = new Customer($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $user->setCustomer($customer);
        }
        $customerAddress = new CustomerAddress();
        $customerAddress->setCustomer($customer);
        $form = $this->createForm(CustomerAddressType::class, $customerAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type = null;
            $message = null;
            $isMainAddress = $form->get('isMainAddress')->getData();
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($customerAddress);
                if($isMainAddress){
                    $customer->setMainAddress($customerAddress);
                }else{
                    $customer->addAddress($customerAddress);
                }
                $entityManager->flush();
                $type = 'success';
                $message = '202 Accepted : The request was accepted';

            } catch (\Exception $e) {
                $type = 'error';
                $message = '400 Bad Request : The server was unable to process the request due to incorrect syntax.' .
                '<br />Message the administrator if the problem persists.' .
                (in_array("ROLE_STOREKEEPER", $this->getUser()->getRoles()))
                    ? '<br />' . $e->getMessage() : '';
            }


            if ($typeResponse == 'json') {
                $addresses = [];
                foreach ($customer->getAddresses() as $address) {
                    $addresses[]=[
                        'id'=>$address->getId(),
                        'mainAddress'=> $address == $user->getcustomer()->getMainAddress(),
                        'address'=>$address->toString()
                    ];
                }

                $status = $type =='success'?200:400;
                return $this->json(['data' => ['message' => $message, 'code' => $status, 'addresses' => $addresses]], $status);
            }

            $this->addFlash($type, $message);
            return $this->redirectToRoute('customer_address_index');
        }


        if ($typeResponse == 'json') {
            $response = $this->render('user/_form.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'title' => 'Update user infos'
            ]);
            return $this->json(['data' => $response->getContent()], 200);
        }

        return $this->render('customer_address/new.html.twig', [
            'customer_address' => $customerAddress,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_address_show", methods={"GET"})
     * @param CustomerAddress $customerAddress
     * @return Response
     */
    public function show(CustomerAddress $customerAddress): Response
    {
        return $this->render('customer_address/show.html.twig', [
            'customer_address' => $customerAddress,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_address_edit", methods={"GET","POST"})
     * @param Request $request
     * @param CustomerAddress $customerAddress
     * @return Response
     */
    public function edit(Request $request, CustomerAddress $customerAddress): Response
    {
        $form = $this->createForm(CustomerAddressType::class, $customerAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_address_index');
        }

        return $this->render('customer_address/edit.html.twig', [
            'customer_address' => $customerAddress,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_address_delete", methods={"DELETE"})
     * @param Request $request
     * @param CustomerAddress $customerAddress
     * @return Response
     */
    public function delete(Request $request, CustomerAddress $customerAddress): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customerAddress->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customerAddress);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
