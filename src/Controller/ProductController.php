<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Infrastructure\Search\Events\EntityCreatedEvent;
use App\Infrastructure\Search\Events\EntityDeletedEvent;
use App\Infrastructure\Search\Events\EntityUpdatedEvent;
use App\Infrastructure\Search\SearchConstants;
use App\Infrastructure\Search\SearchInterface;
use App\Services\EntityToModelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    private EventDispatcherInterface $dispatcher;
    private EntityToModelService $entityToModel;

    public function __construct(EventDispatcherInterface $dispatcher, EntityToModelService $entityToModel)
    {
        $this->dispatcher = $dispatcher;
        $this->entityToModel = $entityToModel;
    }

    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(Request $request, SearchInterface $search): Response
    {
        $user = $this->getUser();

        $q = $request->query->get('q', '');
        $min = $request->query->getInt('min');
        $max = $request->query->getInt('max');

        $products = $search->search(SearchConstants::PRODUCTS_INDEX, $q, [
            'range' => [
                'field' => 'price', 
                'min' => $min, 
                'max' => $max 
            ],
            'search_in' => ['name', 'description'],
            'close' => [
                'origin' => $user->getLocation(),// Morocco []
                'to' => 'store.location',
            ]
        ]);

        // filter by categories

        return $this->render('product/index.html.twig', [
            'products' => $products->getItems(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->dispatcher->dispatch(new EntityCreatedEvent(
                SearchConstants::PRODUCTS_INDEX, 
                $this->getParameter('root_dir'), 
                $this->entityToModel->product($product, true, true),
                SearchConstants::TYPESENSE
            ));

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $previous = clone $product;

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $content = $this->entityToModel->product($product, true, true);
            $previousContent = $this->entityToModel->product($previous, true, true);

            if ($previousContent !== $content) {
                $this->dispatcher->dispatch(new EntityUpdatedEvent(
                    SearchConstants::PRODUCTS_INDEX, 
                    $this->getParameter('root_dir'), 
                    $content,
                SearchConstants::TYPESENSE));
            }

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $this->dispatcher->dispatch(new EntityDeletedEvent(SearchConstants::PRODUCTS_INDEX, (string) $product->getId(), SearchConstants::TYPESENSE));
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
