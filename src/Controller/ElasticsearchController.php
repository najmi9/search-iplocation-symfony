<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Elastica\Query\MultiMatch;
use JoliCode\Elastically\Client;
use JoliCode\Elastically\Messenger\IndexationRequest;
use JoliCode\Elastically\Messenger\IndexationRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ElasticsearchController extends AbstractController
{
    /**
     * @Route("/elasticsearch", name="elasticsearch")
     */
    public function index(Request $request, ProductRepository $productRepo, Client $client): Response
    {
        $query = $request->query->get('q', '');

        $searchQuery = new MultiMatch();
        $searchQuery->setFields([
            'name^5',
            'name.autocomplete',
        ]);
        $searchQuery->setQuery($query);
        $searchQuery->setType(MultiMatch::TYPE_MOST_FIELDS);

        $foundPosts = $client->getIndex('product')->search($searchQuery);

        $data = [];

        foreach ($foundPosts->getResults() as $result) {
            // $data[] = $result->getModel();
        }

        dd($data);

        return $this->render('elasticsearch/index.html.twig', [
            'controller_name' => 'ElasticsearchController',
        ]);
    }


    /**
     * @Route("/index-product/{id}", name="index_product")
     */
    public function indexProduct(MessageBusInterface $bus, Product $product)
    {
        // Third argument is the operation, so for a delete:
        // new IndexationRequest(Product::class, 'ref9999', IndexationRequestHandler::OP_DELETE);

        $bus->dispatch(new IndexationRequest(\App\Model\Product::class, $product->getId(), IndexationRequestHandler::OP_INDEX));

        return $this->render('elasticsearch/index.html.twig', [
            'controller_name' => 'ElasticsearchController',
        ]);
    }
}
