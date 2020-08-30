<?php

namespace App\Tests\Helpers;

class PayloadReaderTest
{
    /**
     * @param string $path
     * @return mixed
     */
    public static function loadPayloadData(string $path)
    {
        $string = file_get_contents($path);
        return json_decode($string, true);
    }

    public static function loadHerokuResponseData()
    {
        return self::loadPayloadData(__DIR__ . '/payloads/heroku_response_data.json');
    }
}
