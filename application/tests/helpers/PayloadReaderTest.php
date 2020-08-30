<?php

namespace App\Tests\Helpers;

class PayloadReaderTest
{
    const HEROKU_RESPONSE_DATA = '/payloads/heroku_response_data.json';
    const HEROKU_RESPONSE_DATA_WITH_FILTER = '/payloads/heroku_response_data_with_filter.json';


    /**
     * @param string $path
     * @return mixed
     */
    public static function loadPayloadArray(string $path): array
    {
        return json_decode(self::loadPayload($path), true);
    }

    public static function loadHerokuResponseArray(): array
    {
        return self::loadPayloadArray(__DIR__ . self::HEROKU_RESPONSE_DATA);
    }

    public static function loadHerokuResponse(): string
    {
        return self::loadPayload(__DIR__ . self::HEROKU_RESPONSE_DATA);
    }

    public static function loadHerokuResponseFilterArray(): array
    {
        return self::loadPayloadArray(__DIR__ . self::HEROKU_RESPONSE_DATA_WITH_FILTER);
    }

    private static function loadPayload(string $path): string
    {
        return file_get_contents($path);
    }
}
