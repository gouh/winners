<?php

namespace App\Controller;

use App\DTO\WinnerDto;
use App\DTO\PaginationDto;
use App\Entity\Winner;
use App\Form\Type\WinnerType;
use App\Repository\WinnerRepository;
use App\Response\ApiResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WinnerController extends AbstractController
{
    public const TRY_AGAIN_MESSAGE = 'Try again latter please.';
    public const WINNER_NOT_FOUND_MESSAGE = 'Winner not found.';

    /**
     * @param WinnerRepository $winnerRepository
     * @param LoggerInterface  $logger
     */
    public function __construct(
        private readonly WinnerRepository $winnerRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    private function persistWinner(Request $request, Winner $winner): array
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(WinnerType::class, $winner);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->winnerRepository->save($winner);
                $response = ApiResponse::createResponse(Response::HTTP_OK, null, WinnerDto::ObjectToArray($winner));
                $this->logger->info(sprintf('[WinnerController] Winner persisted: %s.', $winner->getName()));
            } catch (Exception $e) {
                $this->logger->error(sprintf('[WinnerController] Error on persist winner: %s.', $e->getMessage()));
                $response = ApiResponse::createResponse(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    self::TRY_AGAIN_MESSAGE,
                );
                if (get_class($e) === UniqueConstraintViolationException::class) {
                    $response = ApiResponse::createResponse(
                        Response::HTTP_BAD_REQUEST,
                        sprintf('Position %s is already used.', $winner->getPosition())
                    );
                }
            }
        } else {
            $response = ApiResponse::createResponse(Response::HTTP_BAD_REQUEST, (string)$form->getErrors(true));
        }

        return $response;
    }

    #[Route('/winners', name: 'get_all_winners', methods: ['get'])]
    public function getAll(Request $request): JsonResponse
    {
        $response = ApiResponse::createResponse(Response::HTTP_INTERNAL_SERVER_ERROR, self::TRY_AGAIN_MESSAGE);
        try {
            $allQueryParams = $request->query->all();

            $itemsPerPage = intval($request->query->get('itemsPerPage', 30));
            $page = intval($request->query->get('page', 1));

            $results = $this->winnerRepository->getPaginated($itemsPerPage, $page, $allQueryParams['orderBy']);
            $total = $this->winnerRepository->getTotal();

            $pagination = PaginationDto::calcPagination($page, count($results), $total, $itemsPerPage);
            $response = ApiResponse::createResponse(
                Response::HTTP_OK,
                null,
                WinnerDto::ObjectsToArray($results),
                $pagination
            );
        } catch (Exception $e) {
            $this->logger->error(sprintf('[WinnerController] Error on get winners: %s', $e->getMessage()));
        }

        return $this->json($response, $response['metadata']['statusCode']);
    }

    #[Route('/winners/{id}', name: 'get_winner_by_id', methods: ['get'])]
    public function getById(int $id): JsonResponse
    {
        $winner = $this->winnerRepository->findOneBy(['id' => $id]);
        if (!$winner) {
            $response = ApiResponse::createResponse(Response::HTTP_NOT_FOUND, self::WINNER_NOT_FOUND_MESSAGE);
        } else {
            $response = ApiResponse::createResponse(Response::HTTP_OK, null, WinnerDto::ObjectToArray($winner));
        }

        return $this->json($response, $response['metadata']['statusCode']);
    }

    #[Route('/winners', name: 'create_winner', methods: ['post'])]
    public function create(Request $request): JsonResponse
    {
        $winner = new Winner();
        $response = $this->persistWinner($request, $winner);

        return $this->json($response, $response['metadata']['statusCode']);
    }

    #[Route('/winners/{id}', name: 'update_winner_by_id', methods: ['put'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $winner = $this->winnerRepository->findOneBy(['id' => $id]);
        if (!$winner) {
            $response = ApiResponse::createResponse(Response::HTTP_NOT_FOUND, self::WINNER_NOT_FOUND_MESSAGE);
        } else {
            $response = $this->persistWinner($request, $winner);
        }

        return $this->json($response, $response['metadata']['statusCode']);
    }

    #[Route('/winners/{id}', name: 'delete_winner_by_id', methods: ['delete'])]
    public function delete(int $id): JsonResponse
    {
        $winner = $this->winnerRepository->findOneBy(['id' => $id]);
        if (!$winner) {
            $response = ApiResponse::createResponse(Response::HTTP_NOT_FOUND, self::WINNER_NOT_FOUND_MESSAGE);
        } else {
            $response = null;
        }

        try {
            $this->winnerRepository->remove($winner);
            $this->logger->info(sprintf('[WinnerController] Winner removed: %s', $id));
        } catch (Exception $e) {
            $this->logger->error(sprintf('[WinnerController] Error on remove winner: %s', $e->getMessage()));
            $response = ApiResponse::createResponse(Response::HTTP_INTERNAL_SERVER_ERROR, self::TRY_AGAIN_MESSAGE);
        }

        return $this->json($response, 204);
    }
}
