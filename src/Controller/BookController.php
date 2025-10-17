<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/api/v1/books', name: 'api_books')]
final class BookController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/search', name: 'route_book_search', methods: ['GET'])]
    public function search(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = [
            'author_id' => $request->query->getInt('author_id'),
            'start_date' => $request->query->get('start_date'),
            'end_date' => $request->query->get('end_date'),
        ];

        $constraints = new Assert\Collection([
            'author_id' => [new Assert\NotBlank(), new Assert\Type('integer'), new Assert\Positive()],
            'start_date' => [new Assert\NotBlank(), new Assert\Date()],
            'end_date' => [new Assert\NotBlank(), new Assert\Date()],
        ]);

        $errors = $validator->validate($data, $constraints);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['message' => 'Validation failed', 'errors' => $errorMessages], 422);
        }
        if ($data['start_date'] >= $data['end_date']) {
            return $this->json([
                'message' => 'Validation failed',
                'errors' => ['start_date' => 'Start date must be before end date.'],
            ], 422);
        }

        // EXTRACTION DES DONNÉES
        $qb = $this->em->getRepository(Book::class)->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->leftJoin(Rent::class, 'r', 'WITH', 'r.book = b.id')
            ->where('a.id = :authorId')
            ->andWhere('r.rentDate BETWEEN :start AND :end')
            ->setParameters([
                'authorId' => $data['author_id'],
                'start' => new \DateTime($data['start_date']),
                'end' => new \DateTime($data['end_date']),
            ]);

        $books = $qb->getQuery()->getArrayResult();


        // RETOUR DE LA RÉPONSE
        return $this->json([
            'query' => $data,
            'data' => $books,
            'length' => count($books),
            'message' => 'ok',
        ]);
    }
}
