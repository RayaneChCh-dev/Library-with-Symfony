<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rent;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/api/v1/books', name: 'api_books_rent')]
final class RentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/rent', name: 'route_book_rent', methods: ['GET'])]
    public function rent(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $userId = $request->query->getInt('user_id');

        $errors = $validator->validate(['user_id' => $userId], new Assert\Collection([
            'user_id' => [new Assert\NotBlank(), new Assert\Type('integer'), new Assert\Positive()],
        ]));

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['message' => 'Validation failed', 'errors' => $errorMessages], 422);
        }

        $rents = $this->em->getRepository(Rent::class)->findBy(['user' => $userId]);

        $data = array_map(fn(Rent $r) => [
            'rent_id' => $r->getId(),
            'book_id' => $r->getBook()->getId(),
            'rent_date' => $r->getRentDate()->format('Y-m-d H:i:s'),
            'return_date' => $r->getReturnDate()?->format('Y-m-d'),
            'actual_return_date' => $r->getActualReturnDate()?->format('Y-m-d H:i:s'),
        ], $rents);

        return $this->json(['user_id' => $userId, 'data' => $data, 'length' => count($data), 'message' => 'ok']);
    }

    #[Route('/rent', name: 'route_book_rent_post', methods: ['POST'])]
    public function rentPost(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) return $this->json(['error' => 'Invalid JSON'], 400);

        $constraints = new Assert\Collection([
            'user_id' => [new Assert\NotBlank(), new Assert\Type('integer'), new Assert\Positive()],
            'book_id' => [new Assert\NotBlank(), new Assert\Type('integer'), new Assert\Positive()],
            'rent_date' => [new Assert\NotBlank(), new Assert\Date()],
            'return_date' => [new Assert\Optional([new Assert\Date()])],
        ]);

        $errors = $validator->validate($data, $constraints);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            return $this->json(['message' => 'Validation failed', 'errors' => $errorMessages], 422);
        }

        $user = $this->em->getRepository(User::class)->find($data['user_id']);
        $book = $this->em->getRepository(Book::class)->find($data['book_id']);

        if (!$user || !$book) return $this->json(['message' => 'User or book not found'], 404);

        $rent = new Rent();
        $rent->setUser($user)->setBook($book)->setRentDate(new \DateTime($data['rent_date']));
        if (!empty($data['return_date'])) $rent->setReturnDate(new \DateTime($data['return_date']));

        $this->em->persist($rent);
        $this->em->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Successfully created rent!',
            'rent_id' => $rent->getId()
        ], 201);
    }

    #[Route('/rent/restore', name: 'route_book_rent_restore', methods: ['POST'])]
    public function rentRestore(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) return $this->json(['error' => 'Invalid JSON'], 400);

        $constraints = new Assert\Collection([
            'user_id' => [new Assert\NotBlank(), new Assert\Type('integer'), new Assert\Positive()],
            'book_id' => [new Assert\NotBlank(), new Assert\Type('integer'), new Assert\Positive()],
        ]);

        $errors = $validator->validate($data, $constraints);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            return $this->json(['message' => 'Validation failed', 'errors' => $errorMessages], 422);
        }

        $rent = $this->em->getRepository(Rent::class)->findOneBy([
            'user' => $data['user_id'],
            'book' => $data['book_id'],
            'actualReturnDate' => null
        ]);

        if (!$rent) return $this->json(['message' => 'Rent not found or already returned'], 404);

        $rent->setActualReturnDate(new \DateTime());
        $this->em->flush();

        return $this->json(['status' => 'success', 'message' => 'Successfully restored rent!'], 200);
    }
}