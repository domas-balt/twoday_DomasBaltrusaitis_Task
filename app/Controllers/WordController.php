<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exception\NotFoundException;
use App\Exception\UnprocessableEntityException;
use App\Helpers\HttpResponseCodes;
use App\Repositories\WordRepository;

readonly class WordController implements ControllerInterface
{
    public function __construct(
        private \PDO $dbConnection,
        private mixed $requestMethod,
        private int $wordId,
        private WordRepository $wordRepository
    ) {
    }

    public function list(): Response
    {
        return new Response();
    }

    public function processRequest(): array
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->wordId) {
                    $response = $this->getWordById($this->wordId);
                } else {
                    $response = $this->getAllWords();
                }
                break;
            case 'POST':
                $response = $this->createWord();

                break;
            case 'PUT':
                $response = $this->updateWord($this->wordId);

                break;
            case 'DELETE':
                $response = $this->deleteWord($this->wordId);

                break;
            default:
                throw new NotFoundException('Not Found');
        }

        header($response['status_code_header']);

        return $response;
    }

    public function getAllWords(): array
    {
        $result = $this->wordRepository->getAllWords();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

    public function getWordById(int $wordId): array
    {
        $wordEntity = $this->wordRepository->getWordById($wordId);
        if($wordEntity === null) {
            throw new NotFoundException('Not Found');
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($wordEntity);
        return $response;
    }

    public function createWord(): array
    {
        $input = (array) json_decode(file_get_contents("php://input", true));

        if (empty($input['text'])) {
            throw new UnprocessableEntityException('Unprocessable Entity');
        }

        $this->wordRepository->insertWord($input['text']);

        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;

        return $response;
    }

    public function updateWord(int $id): array
    {
        $wordEntity = $this->wordRepository->getWordById($id);
        if ($wordEntity === null) {
            throw new NotFoundException('Not Found');
        }

        $input = (array) json_decode(file_get_contents("php://input", true));

        if (
            !isset($input['text'])
        ) {
            throw new UnprocessableEntityException('Unprocessable Entity');
        }

        $this->wordRepository->updateWord($id, $input['text']);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;

        return $response;
    }

    public function deleteWord(int $id): array
    {
        $wordEntity = $this->wordRepository->getWordById($id);
        if ($wordEntity === null) {
            throw new NotFoundException('Not Found');
        }

        $this->wordRepository->deleteWord($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;

        return $response;
    }
}
