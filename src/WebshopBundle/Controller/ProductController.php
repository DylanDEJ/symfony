<?php


namespace WebshopBundle\Controller;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WebshopBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use WebshopBundle\Service\FileUploader;
use WebshopBundle\Form\ProductType;


/**
 * Product controller.
 *
 * @Route("product")
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities.
     *
     * @Route("/", name="product_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('WebshopBundle:Product')->findAll();

        return $this->render('@Webshop/product/index.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * Creates a new product entity.
     *
     * @Route("/new", name="product_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('WebshopBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fileUploader = new FileUploader($this->getParameter('image_directory'));
            // $file stores the uploaded PDF file
            /** @var UploadedFile $file */
            $file = $product->getImagepath();
            $fileName = $fileUploader->upload($file);
            $product->setImagepath($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirect($this->generateUrl('product_index'));


        }

        return $this->render('@Webshop/product/new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("/{id}", name="product_show")
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('@Webshop/product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        if($product->getImagepath()) {
            $product->setImagepath(
                (new File( $this->getParameter( 'image_directory').'/'.$product->getImagepath()))
            );
        }
        $editForm = $this->createForm('WebshopBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $fileUploader = new FileUploader($this->getParameter('image_directory'));
            // $file stores the uploaded PDF file
            /** @var UploadedFile $file */
            $file = $product->getImagepath();
            $fileName = $fileUploader->upload($file);
            $product->setImagepath($fileName);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
        }

        return $this->render('@Webshop/product/edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     *
     * @Route("/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($product->getImagepath())) {
                unlink( $this->getParameter('image_directory').'/'. $product->getImagepath());
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }


        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Deletes a product entity.
     *
     * @Route("/{id}", name="product_guestdelete")
     * @Method("DELETE")
     */
    public function guestdeleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($product->getImagepath())) {
                unlink( $this->getParameter('image_directory').'/'. $product->getImagepath());
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }


        return $this->redirectToRoute('product_index');
    }
}
