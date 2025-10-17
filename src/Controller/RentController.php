<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/v1/books', name: 'api_books_rent')]
final class RentController extends AbstractController
{
    // GET /api/v1/books/rent?user_id={user_id}
    // Récupère les emprunts d'un utilisateur
    #[Route('/rent', name: 'route_book_rent', methods: ['GET'])]
    public function rent(Request $request): JsonResponse
    {
        $userQuery = $request->query->getInt('user_id', 1);

        return $this->json([
            'user_id' => $userQuery,
            'data' => [
                // Résultats fictifs pour l'exemple (trié de la plus ancienne à la plus récente)
                [
                    'rent_id' => 1,
                    'book_id' => 2,
                    'user_id' => $userQuery,
                    'rent_date' => '2023-05-10',
                    'return_date' => null,
                ],
            ],
            'length' => 1,
            'message' => 'ok',
        ]);
    }

    // POST /api/v1/books/rent?user_id={user_id}&book_id={book_id}&rent_date={rent_date}&return_date={return_date}
    // Créer une location (emprunter un livre)
    #[Route('/rent', name: 'route_book_rent_post', methods: ['POST'])]
    public function rentPost(Request $request): JsonResponse
    {
        $userId = $request->request->getInt('user_id', 1);
        $bookId = $request->request->getInt('book_id', 1);
        $rentDate = $request->request->get('rent_date', date('Y-m-d'));
        $returnDate = $request->request->get('return_date');

        return $this->json([
            'query' => [
                'user_id' => $userId,
                'book_id' => $bookId,
                'rent_date' => $rentDate,
                'return_date' => $returnDate,
            ],
            'status' => 'success',
            'message' => 'Successfully created rent!',
        ], 201);
    }

    // POST /api/v1/books/rent/restore?user_id={user_id}&book_id={book_id}
    // Rendre une location (retourner un livre)
    #[Route('/rent/restore', name: 'route_book_rent_restore', methods: ['POST'])]
    public function rentRestore(Request $request): JsonResponse
    {
        $userId = $request->request->getInt('user_id', 1);
        $bookId = $request->request->getInt('book_id', 1);

        return $this->json([
            'query' => [
                'user_id' => $userId,
                'book_id' => $bookId,
            ],
            'status' => 'success',
            'message' => 'Successfully restored rent!',
        ], 200);
    }
}
