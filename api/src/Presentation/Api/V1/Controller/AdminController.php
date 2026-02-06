<?php

declare(strict_types=1);

namespace App\Presentation\Api\V1\Controller;

use App\Application\DTO\CreateUserAdminRequestDTO;
use App\Application\DTO\UpdateUserAdminRequestDTO;
use App\Application\UseCase\CreateUserAdminUseCase;
use App\Application\UseCase\DeleteUserUseCase;
use App\Application\UseCase\GetUserUseCase;
use App\Application\UseCase\ListUsersUseCase;
use App\Application\UseCase\UpdateUserAdminUseCase;
use App\Domain\Exception\ConflictException;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\ValidationException;
use App\Infrastructure\Http\Response\JsonResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Throwable;

class AdminController
{
    public function __construct(
        private readonly JsonResponseFactory $jsonResponseFactory,
        private readonly LoggerInterface $logger,
        private readonly CreateUserAdminUseCase $createUserAdminUseCase,
        private readonly ListUsersUseCase $listUsersUseCase,
        private readonly GetUserUseCase $getUserUseCase,
        private readonly UpdateUserAdminUseCase $updateUserAdminUseCase,
        private readonly DeleteUserUseCase $deleteUserUseCase,
    ) {
    }

    public function createUser(Request $request): Response
    {
        $data = $request->getParsedBody();
        $dto = CreateUserAdminRequestDTO::fromArray($data);

        try {
            $userResponseDto = $this->createUserAdminUseCase->execute($dto);

            return $this->jsonResponseFactory->success($userResponseDto->jsonSerialize(), 'Usuário criado com sucesso.', 201);
        } catch (ConflictException | NotFoundException | ValidationException $e) {
            $this->logger->warning('Falha na criação de usuário admin: ' . $e->getMessage());

            $errors = $e instanceof ValidationException ? $e->getErrors() : null;
            return $this->jsonResponseFactory->fail($errors, $e->getMessage(), $e->getStatusCode());
        } catch (Throwable $e) {
            $this->logger->error('Ocorreu um erro inesperado durante a criação de usuário admin', ['exception' => $e]);

            return $this->jsonResponseFactory->error('Ocorreu um erro inesperado.');
        }
    }

    public function listUsers(Request $request): Response
    {
        $params = $request->getQueryParams();
        $limit = isset($params['limit']) ? (int)$params['limit'] : 20;
        $offset = isset($params['offset']) ? (int)$params['offset'] : 0;

        $userListResponseDTO = $this->listUsersUseCase->execute($limit, $offset);

        return $this->jsonResponseFactory->success($userListResponseDTO);
    }

    public function getUser(Request $request, Response $response, array $args): Response
    {
        $userId = (int)$args['id'];

        try {
            $userResponseDto = $this->getUserUseCase->execute($userId);

            return $this->jsonResponseFactory->success($userResponseDto->jsonSerialize());
        } catch (NotFoundException $notFoundException) {
            return $this->jsonResponseFactory->fail(null, $notFoundException->getMessage(), 404);
        }
    }

    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $userId = (int)$args['id'];
        $data = $request->getParsedBody();
        $dto = UpdateUserAdminRequestDTO::fromArray($userId, $data);

        try {
            $userResponseDto = $this->updateUserAdminUseCase->execute($dto);

            return $this->jsonResponseFactory->success($userResponseDto->jsonSerialize(), 'Usuário atualizado com sucesso.');
        } catch (NotFoundException | ConflictException | ValidationException $e) {
            $this->logger->warning('Falha na atualização de usuário admin: ' . $e->getMessage());

            $errors = $e instanceof ValidationException ? $e->getErrors() : null;
            return $this->jsonResponseFactory->fail($errors, $e->getMessage(), $e->getStatusCode());
        } catch (Throwable $e) {
            $this->logger->error('Ocorreu um erro inesperado durante a atualização de usuário admin', ['exception' => $e]);

            return $this->jsonResponseFactory->error('Ocorreu um erro inesperado.');
        }
    }

    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        $userId = (int)$args['id'];
        $requestingUserId = $request->getAttribute('user_id');

        if ($userId === (int)$requestingUserId) {
            return $this->jsonResponseFactory->fail(null, 'Administradores não podem excluir a própria conta.', 403);
        }

        try {
            $this->deleteUserUseCase->execute($userId);

            return $this->jsonResponseFactory->success(null, 'Usuário excluído com sucesso.');
        } catch (NotFoundException $e) {
            return $this->jsonResponseFactory->fail(null, $e->getMessage(), 404);
        } catch (Throwable $e) {
            $this->logger->error('Ocorreu um erro inesperado durante a exclusão de usuário', ['exception' => $e]);

            return $this->jsonResponseFactory->error('Ocorreu um erro inesperado.');
        }
    }
}
