<?php

namespace App;

use GuzzleHttp\Promise\Promise;

class Setup
{
    /**
     * @var \Aws\DynamoDb\DynamoDbClient
     */
    protected $client;

    public function __construct()
    {
        $this->client = DynamoDbClientFactory::create();
    }

    public function run(): void
    {
        echo "Running Setup. \n";

        $this->createTable();
        $this->populateDatabase();
    }

    protected function createTable(): void
    {
        if ($this->tableExists('myTable')) {
            echo "Table already exists. Skipping. \n";

            return;
        }

        echo "Creating table… \n";

        $this->client->createTable([
            'TableName' => 'myTable',

            'AttributeDefinitions' => [
                ['AttributeName' => 'myKey', 'AttributeType' => 'S'],
                ['AttributeName' => 'myRange', 'AttributeType' => 'S'],
            ],

            'KeySchema' => [
                ['AttributeName' => 'myKey', 'KeyType' => 'HASH'],
                ['AttributeName' => 'myRange', 'KeyType' => 'RANGE'],
            ],

            'ProvisionedThroughput' => [
                'ReadCapacityUnits'  => 10,
                'WriteCapacityUnits' => 5,
            ],

            'GlobalSecondaryIndexes' => [
                [
                    'IndexName'             => 'myRangeIndex',
                    'KeySchema'             => [
                        [
                            'AttributeName' => 'myRange',
                            'KeyType'       => 'HASH',
                        ],
                    ],
                    'Projection'            => [
                        'ProjectionType' => 'ALL',
                    ],
                    'ProvisionedThroughput' => [
                        'ReadCapacityUnits'  => 10,
                        'WriteCapacityUnits' => 5,
                    ],
                ],
            ],
        ]);

        $this->client->waitUntil('TableExists', [
            'TableName' => 'myTable',
        ]);
    }

    protected function tableExists(string $table): bool
    {
        try {
            $result = $this->client->describeTable(['TableName' => $table]);
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $exception) {
            $prev = $exception->getPrevious();
            if (
                $prev instanceof \GuzzleHttp\Exception\ClientException
                && $prev->getCode() === 400
            ) {
                return false;
            }

            throw $exception;
        }

        $table = $result->get('Table');

        if (!empty($table) && !empty($table['TableName'])) {
            return true;
        }

        return false;
    }

    private function populateDatabase(): void
    {
        for ($i = 1; $i <= 200; $i++) {

            echo "Creating 10 items ($i / 200)… \n";

            /** @var Promise[] $promises */
            $promises = [];

            for ($j = 0; $j < 10; $j++) {
                $promises[] = $this->client->putItemAsync([
                    'TableName' => 'myTable',
                    'Item'      => [
                        'myKey'   => ['S' => "key-$i-$j"],
                        'myRange' => ['S' => "default-range"],
                        'myValue' => ['S' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'],
                    ],
                ]);
            }

            foreach ($promises as $promise) {
                $promise->wait(); // Wait for them to finish so we don't use too much memory.
            }
        }

        echo "total of 2000 items created";
    }
}