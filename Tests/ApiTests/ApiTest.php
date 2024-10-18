<?php

declare(strict_types=1);

namespace Tests\ApiTests;
use App\Database\DatabaseConnection;
use App\Services\FileService;
use PHPUnit\Framework\TestCase;

abstract class ApiTest extends TestCase
{
    protected static \PDO $connection;

    public static function setUpBeforeClass(): void
    {
        FileService::readEnvFile('/Server/var/.env');

        self::$connection = DatabaseConnection::tryConnect();
    }

    protected function makeGetRequest(string $uri): mixed
    {
        $context_options = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Content-Type' => 'application/json',
                ]
            ]
        ];

        $context = stream_context_create($context_options);
        $stream = fopen($uri, 'r', false, $context);

        return json_decode(stream_get_contents($stream), true);
    }
}
