<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderItem;
use App\Repository\OrderItemRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(OrderItemRepository $orderItemReposotory , Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('cart/cart.html.twig', [
            'orders' => $orderItemReposotory->getUserItemNotOrdered($user)
        ]);
    }
    
    #[Route('/cart/add/{gameId}', name: 'app_cart_add', methods: ['GET', 'POST'])]
    public function addToCart($gameId,GameRepository $gameRepository , EntityManagerInterface $entityManager , Security $security , Request $request): Response
    {
        $user = $security->getUser();
        $orderItem = new OrderItem();
        $game = $gameRepository->find($gameId);
        $orderItem->setProduct($game);
        $orderItem->setUser($user);

        $entityManager->persist($orderItem);
        $entityManager->flush();

        $referer = $request->headers->get('referer');
        if ($referer) {
            return new RedirectResponse($referer);
        }
        return new Response('L\'article a été ajouté au panier.', Response::HTTP_OK);
    }
    #[Route('/cart/{id}', name: 'app_cart_delete', methods: ['POST'])]
    public function delete(Request $request, OrderItem $orderItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orderItem->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($orderItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
    /*#[Route('/cart/checkout', name: 'app_checkout' , methods: ['GET', 'POST'])]
    */
    #[Route('/order/new', name: 'app_checkout', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,OrderItemRepository $orderItemReposotory , Security $security): Response
    {
        $order = new Order();
        $user = $security->getUser();
        $orderItems = $orderItemReposotory->getUserItemNotOrdered($user);

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentMethode = $request->request->get('payment_type');
            $prixTotal = $request->request->get('prix_total');
            
            $order->setUser($user);
            $order->setCreateAt(new \DateTime());
            $order->setPaymentType($paymentMethode);
            $order->setPrixTotal($prixTotal);
            $order->addOrderItems($orderItems);

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cart/checkout.html.twig', [
            'user' => $user,
            'orders' => $orderItems,
            'order' => $order,
            'form' => $form,
            'items_count' => count($orderItems)
        ]);
    }
}

