<?php

declare(strict_types=1);

namespace App\Controller;

use App\Infrastructure\Search\SearchInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(Request $request, SearchInterface $search): Response
    {
        $page = $request->query->getInt('page', 1);

        $q = $request->query->get('q', '');

        $limit = $request->query->getInt('limit', 6);

        $options = [];

        $options['highlight_full_fields'] = ['name', 'shortDescription'];

        $options['range'] = [
            'field' => 'price',
            'min' => 10,
            'max' => 50,
        ];

        $options['search_in'] = [
            'name',
            'description'
        ];

        $options['close'] = [
            'to' => 'store.location',
            'origin' => $this->getUser()->getLocation(),
        ];

        $result = $search->search('product', $q, $options, $limit, $page);

        return $this->render('search/index.html.twig', [
            'items' => $result->getItems(),
            'total' => $result->getTotal(),
            'q' => $q,
        ]);
    }
}
