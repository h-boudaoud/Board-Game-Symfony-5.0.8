<?php


namespace App\Controller;


use App\Entity\CustomerAddress;
use App\Repository\CustomerAddressRepository;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var GameRepository
     */
    private $gameRepository;
    /**
     * @var CustomerAddressRepository
     */
    private $addressRepository;

    public function __construct(
        ObjectManager $manager,
        UserRepository $userRepository,
        CustomerAddressRepository $addressRepository,
        GameRepository $gameRepository
    )
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->gameRepository = $gameRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @Route("/", name="cart_index", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $typeResponse = strstr($request->headers->get('accept'), 'json') ? 'json' : 'html';
        $cart = $request->request->get('orders');
        $orders = null;
        $total = null;
        $customer = null;
        $errorMessage = null;
        if ($cart) {
            $result = $this->myFunction($cart);
            $orders = $result['orders'];
            $total = $result['total'];
        }
        $id = intval($request->request->get('userId'));
        $user = $this->userRepository->find($id);

        if($user) {
            $customer = $user->getCustomer();
        }else{
            $errorMessage = "The ID = '$id' does not match any user ID in database";
        }

        $data = [
            //'user' => $user,
            'orders' => $orders,
            'total' => $total,
            'cart' => $cart,
            'title' => 'cart',
            'customer' => $customer,
            'errorMessage'=>$errorMessage
        ];

        // dd($data);


        if ($typeResponse == 'json') {
            $responseHTML = $errorMessage;
            $responseJS = null;
            $status = 400;
            if ($user) {
                $responseHTML = $this->render('cart/_cart.html.twig', $data)->getContent();
                $responseJS = $this->render('cart/_cart.js.twig', $data)->getContent();
                $status = 200;
            }
//            dd([
//                'html' =>  $this->render('cart/_cart.html.twig', $data)->getContent(),
//                'js' => $responseJS,
//                'title' => 'Cart',
//                $data
//            ]);
            return $this->json([
                'data' => [
                    'html' => $responseHTML,
                    'js' => $responseJS,
                    'title' => 'Cart'
                ]
            ], $status);
        }
        //dd(['orders' => $orders, 'total' => $total]);
        return $this->render('cart/index.html.twig', $data);
    }

    /**
     * @Route("/orders", name="cart_orders", methods={"POST"})
     * @param Request $request
     * @param GameRepository $gameRepository
     * @return Response
     * @throws \Exception
     */
    public function orders(Request $request, GameRepository $gameRepository)
    {
        $address = $this->addressRepository->find(intval($request->request->get('address')));
        if (!$address) {
            $message = '400 Bad Request : The server was unable to process the request due to incorrect syntax.' .
                '<br />User not defined .';
            $this->addFlash('error', $message);
            return $this->redirectToRoute('login', [], 301);
        } else {
            $cart = $request->request->get('orders');
            $orders = $this->myFunction($cart)['orders'];
            $total = $this->myFunction($cart)['total'];
            $ref = uniqid();


            return $this->render('cart/orders.html.twig', [
                'deliveredAt' => null,
                'dispatchedAt' => (new \DateTime('+ 4 day')),
                'commandedAt' => null, //new \DateTime(),
                'title' => 'cart',
                'address' => $address,
                'orders' => $orders,
                'total' => $total,
                'ref' => $ref,
                'route' => 'cart'
            ]);

        }
    }

    private function myFunction($cart)
    {
        $total = 0;
        $orders = [];
        foreach ($cart as $id => $value) {
            $order = [];
            $game = $this->gameRepository->find($id);

            $order['gameId'] = $game->getGameId();
            $order['name'] = $game->getName();
            $order['price'] = $game->getPrice();
            $order['quantity'] = $value;
            $order['discount'] = $game->getDiscount();
            $order['total'] = $game->getPrice() * (1 + $game->getDiscount()) * $order['quantity'];

            $total += $order['total'];
            $orders[] = $order;
        }

        //dd(['orders'=>$orders, 'total'=>$total]);
        return ['orders' => $orders, 'total' => $total];

    }
}
