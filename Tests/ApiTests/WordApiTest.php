<?php

declare(strict_types=1);

namespace Tests\ApiTests;

use App\Database\QueryBuilder\MySqlQueryBuilder;
use App\Repositories\WordRepository;
use PHPUnit\Framework\Attributes\DataProviderExternal;

class WordApiTest extends ApiTest
{
    private static WordRepository $wordRepository;

    public function setUp(): void
    {
        $data = [
            [
                'id' => 1,
                'text' => 'testword1',
            ],
            [
                'id' => 2,
                'text' => 'testword2',
            ]
        ];

        foreach ($data as $word) {
            self::$wordRepository->insertWord($word['text']);
        }
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $queryBuilder = new MySqlQueryBuilder();
        self::$wordRepository = new WordRepository($queryBuilder, self::$connection);
    }

    protected function tearDown(): void
    {
        $this->truncateTables();
    }

    public function testGetAll(): void
    {
        $uri = getenv('APP_URI') . '/words';

        $data = $this->makeGetRequest($uri);

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

    #[DataProviderExternal(WordApiDataProvider::class ,'testGet')]
    public function testGet(int $id, string $expectedValue): void
    {
        $uri = getenv('APP_URI') .  "/words/{$id}";

        $data = $this->makeGetRequest($uri);

        $this->assertSame($expectedValue, $data['text']);
    }

    private function truncateTables(): void
    {
        $tables = self::$connection->prepare('SHOW TABLES');
        $tables->execute();

        $this->setForeignKeyChecks(0);

        foreach($tables->fetchAll(\PDO::FETCH_COLUMN) as $table) {
            self::$connection->query('TRUNCATE TABLE `' . $table . '`')->execute();
        }

        $this->setForeignKeyChecks(1);
    }

    private function setForeignKeyChecks(int $flag): void
    {
        self::$connection->query("SET FOREIGN_KEY_CHECKS=$flag;");
    }
}
