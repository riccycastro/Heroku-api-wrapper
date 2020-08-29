<?php

namespace App\Service\Interfaces;

use App\Model\HerokuAppSearch;

interface DemographicServiceInterface
{
    /**
     * @param string $endpointName
     * @param HerokuAppSearch $herokuAppSearch
     * @return array
     */
    public function getData(string $endpointName, HerokuAppSearch $herokuAppSearch): array;
}
