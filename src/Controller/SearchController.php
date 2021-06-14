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

        $options = [
            'query_by' => 'name,shortDescription,category',
            'highlight_full_fields' => 'name,shortDescription',
        ];

        $result = $search->search('products', $q, $options, $limit, $page);

        return $this->render('search/index.html.twig', [
            'items' => $result->getItems(),
            'total' => $result->getTotal(),
            'q' => $q,
        ]);
    }
}
