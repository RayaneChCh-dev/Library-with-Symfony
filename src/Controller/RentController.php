<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/books', name: 'api_books_rent')]
final class RentController extends AbstractController
{
    // GET /api/v1/books/rent?user_id={user_id}
    // Récupère les emprunts d'un utilisateur
    #[Route('/rent', name: 'route_book_rent', methods: ['GET'])]
    public function rent(Request $request, ValidatorInterface $validator): JsonResponse
    {
        // VALIDATION DES DONNÉES
        $data = [
            'user_id' => $request->query->getInt('user_id')
        ];

        $constraints = new Assert\Collection([
            'user_id' => [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
                new Assert\Positive(),
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

        // EXTRACTION DES DONNÉES

        // RETOUR DE LA RÉPONSE
        return $this->json([
            'user_id' => $data['user_id'],
            'data' => [
                // Résultats fictifs pour l'exemple (trié de la plus ancienne à la plus récente)
                [
                    'rent_id' => 1,
                    'book_id' => 2,
                    'user_id' => $data['user_id'],
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
    public function rentPost(Request $request, ValidatorInterface $validator): JsonResponse
    {
        // VALIDATION DES DONNÉES
        $content = $request->getContent();
        $data = json_decode($content, true);
        if ($data === null) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $constraints = new Assert\Collection([
            'user_id' => [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
                new Assert\Positive(),
            ],
            'book_id' => [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
                new Assert\Positive(),
            ],
            'rent_date' => [
                new Assert\NotBlank(),
                new Assert\Date(),
            ],
            'return_date' => [
                new Assert\Optional([
                    new Assert\Date(),
                ]),
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

        // EXTRACTION DES DONNÉES

        // RETOUR DE LA RÉPONSE
        return $this->json([
            'query' => [
                'user_id' => $data['user_id'],
                'book_id' => $data['book_id'],
                'rent_date' => $data['rent_date'],
                'return_date' => $data['return_date']??null,
            ],
            'status' => 'success',
            'message' => 'Successfully created rent!',
        ], 201);
    }

    // POST /api/v1/books/rent/restore?user_id={user_id}&book_id={book_id}
    // Rendre une location (retourner un livre)
    #[Route('/rent/restore', name: 'route_book_rent_restore', methods: ['POST'])]
    public function rentRestore(Request $request, ValidatorInterface $validator): JsonResponse
    {
        // VALIDATION DES DONNÉES
        $content = $request->getContent();
        $data = json_decode($content, true);
        if ($data === null) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $constraints = new Assert\Collection([
            'user_id' => [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
                new Assert\Positive(),
            ],
            'book_id' => [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
                new Assert\Positive(),
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

        // EXTRACTION DES DONNÉES

        // RETOUR DE LA RÉPONSE
        return $this->json([
            'query' => [
                'user_id' => $data['user_id'],
                'book_id' => $data['book_id'],
            ],
            'status' => 'success',
            'message' => 'Successfully restored rent!',
        ], 200);
    }
}
