<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/v1/books', name: 'api_books', methods: ['GET'])]
final class BookController extends AbstractController
{
    // GET /api/v1/books/search?author_id={author_id}&start_date={start_date}&end_date={end_date}
    // Récupère les livres empruntés par un auteur dans une période donnée
    #[Route('/search', name: 'route_book_search')]
    public function search(Request $request): JsonResponse
    {
        $authorQuery = $request->query->getInt('author_id', 1);
        $startDate = $request->query->get('start_date', date('Y-m-d'));
        $endDate = $request->query->get('end_date', date('Y-m-d'));

        return $this->json([
            'query' => [
                'author_id' => $authorQuery,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'data' => [
                // Résultats fictifs pour l'exemple
                [
                    'book_id' => 1,
                    'title' => 'Example Book',
                    'author_id' => $authorQuery,
                    'borrowed_date' => '2020-06-15',
                ],
            ],
            'length' => 1,
            'message' => 'ok',
        ]);
    }
}
