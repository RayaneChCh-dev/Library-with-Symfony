<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api/v1/books', name: 'api_books', methods: ['GET'])]
final class BookController extends AbstractController
{
    // GET /api/v1/books/search?author_id={author_id}&start_date={start_date}&end_date={end_date}
    // Récupère les livres empruntés par un auteur dans une période donnée
    #[Route('/search', name: 'route_book_search')]
    public function search(Request $request, ValidatorInterface $validator): JsonResponse
    {
        // VALIDATION DES DONNÉES
        $data = [
            'author_id' => $request->query->getInt('author_id'),
            'start_date' => $request->query->get('start_date'),
            'end_date' => $request->query->get('end_date'),
        ];

        $constraints = new Assert\Collection([
            'author_id' => [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
                new Assert\Positive(),
            ],
            'start_date' => [
                new Assert\NotBlank(),
                new Assert\Date(),
            ],
            'end_date' => [
                new Assert\NotBlank(),
                new Assert\Date(),
            ],
        ]);
        $errors = $validator->validate($data, $constraints);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages,
            ], 422);
        }
        if ($data['start_date'] >= $data['end_date']) {
            return $this->json([
                'message' => 'Validation failed',
                'errors' => [
                    'start_date' => 'Start date must be before end date.',
                ],
            ], 422);
        }

        // EXTRACTION DES DONNÉES

        // RETOUR DE LA RÉPONSE
        return $this->json([
            'query' => [
                'author_id' => $data['author_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ],
            'data' => [
                [
                    'book_id' => 1,
                    'title' => 'Example Book',
                    'author_id' => $data['author_id'],
                    'borrowed_date' => '2020-06-15',
                ]
            ],
            'length' => 1,
            'message' => 'ok',
        ]);
    }
}
