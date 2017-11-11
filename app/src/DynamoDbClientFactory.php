<?php

namespace App;

use Aws\DynamoDb\DynamoDbClient;

class DynamoDbClientFactory
{
    /**
     * @return DynamoDbClient
     */
    public static function create()
    {
        return new DynamoDbClient(static::config());
    }

    /**
     * @return array
     */
    private static function config()
    {
        return require __DIR__.'/../config/config.php';
    }
}