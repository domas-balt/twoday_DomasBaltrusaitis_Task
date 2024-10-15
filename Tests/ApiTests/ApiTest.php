<?php

declare(strict_types=1);

namespace ApiTests\ApiTest;

use App\Database\DatabaseConnection;
use App\Database\QueryBuilder\MySqlQueryBuilder;
use App\Repositories\WordRepository;
use App\Services\FileService;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private static \PDO $connection;
    private static WordRepository $wordRepository;

    public function setUp(): void
    {
        $data = [
            [
                'id' => 1,
                'text' => 'testword1'
            ],
            [
                'id' => 2,
                'text' => 'testword2'
            ]
        ];

        foreach ($data as $word) {
            self::$wordRepository->insertWord($word['text']);
        }
    }

    public static function setUpBeforeClass(): void
    {
        FileService::readEnvFile('/var/.env');
        $queryBuilder = new MySqlQueryBuilder();

        self::$connection = DatabaseConnection::tryConnect();
        self::$wordRepository = new WordRepository($queryBuilder, self::$connection);
    }

    public function tearDown(): void
    {
        $tables = self::$connection->prepare('SHOW TABLES');
        $tables->execute();

        self::$connection->query('SET FOREIGN_KEY_CHECKS=0;');

        foreach($tables->fetchAll(\PDO::FETCH_COLUMN) as $table) {
            self::$connection->query('TRUNCATE TABLE `' . $table . '`')->execute();
        }

        self::$connection->query('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function testGetAll(): void
    {
        $uri = 'http://127.0.0.1:8000/words';

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

        $data = json_decode(stream_get_contents($stream), true);

        $expectedValues = [
            [
                'id' => 1,
                'text' => 'testword1'
            ],
            [
                'id' => 2,
                'text' => 'testword2'
            ]
        ];

        $this->assertSame($expectedValues, $data);
    }
}
