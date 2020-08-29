<?php

namespace App\Service\Interfaces;

interface HerokuAppClientInterface
{
    /**
     * @param string $endpointName
     * @return array
     */
    public function handleGetData(string $endpointName): array;
}
