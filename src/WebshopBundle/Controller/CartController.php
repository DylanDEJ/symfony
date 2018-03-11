<?php

namespace WebshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use WebshopBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/cart")
 */

class CartController extends Controller
{
    /**
     * @Route("/", name="cart")
     */
    public function indexAction()
    {
        // get the cart from  the session
        $session = new Session();
        // $cart = $session->set('cart', '');
        $cart = $session->get('cart', array());

        // $cart = array_keys($cart);
        // print_r($cart); die;

        // fetch the information using query and ids in the cart
        if( $cart != '' ) {

            foreach( $cart as $id => $quantity ) {
                $productIds[] = $id;

            }

            if( isset( $productIds ) )
            {
                $em = $this->getDoctrine()->getEntityManager();
                $product = $em->getRepository('WebshopBundle:Product')->findById( $productIds );
            } else {
                return $this->render('@Webshop/Cart/index.html.twig', array(
                    'empty' => true,
                ));
            }



            return $this->render('@Webshop/Cart/index.html.twig',     array(
                'product' => $product,
            ));
        } else {
            return $this->render('@Webshop/Cart/index.html.twig',     array(
                'empty' => true,
            ));
        }
    }


    /**
     * @Route("/add/{id}", name="cart_add")
     */
    public function addAction($id)
    {
        // fetch the cart
        $em = $this->getDoctrine()->getEntityManager();
        $product = $em->getRepository('WebshopBundle:Product')->find($id);
        //print_r($product->getId()); die;
        $session = new Session();
        $cart = $session->get('cart', array());


        // check if the $id already exists in it.
        if ( $product == NULL ) {
            $this->get('session')->getFlashBag()->set('notice', 'This product is not     available in Stores');
            return $this->redirect($this->generateUrl('cart'));
        } else {
            if( isset($cart[$id]) ) {

                $qtyAvailable = $product->getQuantity();

                if( $qtyAvailable >= $cart[$id] + 1 ) {
                    $cart[$id] = $cart[$id] + 1;
                } else {
                    $this->get('session')->setFlashBag()->set('notice', 'Quantity     exceeds the available stock');
                    return $this->redirect($this->generateUrl('cart'));
                }
            } else {
                // if it doesnt make it 1
                $cart = $session->get('cart', array());
                $cart[$id] = 1;
            }

            $session->set('cart', $cart);
            return $this->redirect($this->generateUrl('cart'));

        }
    }


    /**
     * @Route("/remove/{id}", name="cart_remove")
     */
    public function removeAction($id)
    {
        // check the cart
        $session = new Session();
        $cart = $session->get('cart', array());

        // if it doesn't exist redirect to cart index page. end
        if(!$cart) { $this->redirect( $this->generateUrl('cart') ); }

        // check if the $id already exists in it.
        if( isset($cart[$id]) ) {
            // if it does ++ the quantity
            $cart[$id] = '0';
            unset($cart[$id]);
            //echo $cart[$id]; die();
        } else {
            $this->get('session')->setFlashBag()->set('notice', 'Go to hell');
            return $this->redirect( $this->generateUrl('cart') );
        }

        $session->set('cart', $cart);

        // redirect(index page)
        $this->get('session')->setFlashBag()->set('notice', 'This product is Remove');
        return $this->redirect( $this->generateUrl('cart') );
    }
}