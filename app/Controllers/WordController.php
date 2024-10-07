<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Word;
use App\Repositories\WordRepository;

readonly class WordController
{
    public function __construct(
        private \PDO $dbConnection,
        private mixed $requestMethod,
        private int $wordId,
        private WordRepository $wordRepository
    ) {
    }

    public function processRequest(): void
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
                $response = $this->notFoundResponse();

                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
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
            return $this->notFoundResponse();
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($wordEntity);
        return $response;
    }

    public function createWord(): array
    {
        $input = (array) json_decode(file_get_contents("php://input", true));

        if (!isset($input['text'])) {
            return $this->notFoundResponse();
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
            return $this->notFoundResponse();
        }

        $input = (array) json_decode(file_get_contents("php://input", true));

        if (
            !isset($input['text'])
            || !isset($input['id'])
        ) {
            $this->unprocessableEntityResponse();
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
            return $this->notFoundResponse();
        }

        $this->wordRepository->deleteWord($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;

        return $response;
    }

    private function notFoundResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;

        return $response;
    }

    private function unprocessableEntityResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);

        return $response;
    }
}
