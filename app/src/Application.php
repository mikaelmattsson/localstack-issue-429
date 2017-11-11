<?php

namespace App;

class Application
{
    /**
     * @var \Aws\DynamoDb\DynamoDbClient
     */
    protected $client;

    public function __construct()
    {
        $this->client = DynamoDbClientFactory::create();
    }

    public function run()
    {
        echo "Running query. Expecting exception.";

        $result = $this->client->query([
            'TableName'     => 'myTable',
            'IndexName'     => 'myRangeIndex',
            'KeyConditions' => [
                'myRange' => [
                    'AttributeValueList' => [['S' => 'default-range']],
                    'ComparisonOperator' => 'EQ',
                ],
            ],
        ]);

        // An exception is thrown before reaching this.

        print_r($result->toArray());
    }
}
