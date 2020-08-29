<?php

namespace App\Service;

use App\Filter\Interfaces\IndicatorFilterInterface;
use App\Model\HerokuAppSearch;
use App\Service\Interfaces\DemographicServiceInterface;
use App\Service\Interfaces\HerokuAppClientInterface;

class DemographicService implements DemographicServiceInterface
{
    /**
     * @var HerokuAppClientInterface
     */
    private $herokuAppClient;

    /**
     * @var IndicatorFilterInterface
     */
    private $indicatorFilter;

    /**
     * DemographicService constructor.
     * @param HerokuAppClientInterface $herokuAppClient
     * @param IndicatorFilterInterface $indicatorFilter
     */
    public function __construct(
        HerokuAppClientInterface $herokuAppClient,
        IndicatorFilterInterface $indicatorFilter
    )
    {
        $this->herokuAppClient = $herokuAppClient;
        $this->indicatorFilter = $indicatorFilter;
    }

    /**
     * @inheritDoc
     */
    public function getData(string $endpointName, HerokuAppSearch $herokuAppSearch): array
    {
        $herokuAppThemes = $this->herokuAppClient->handleGetData($endpointName);

        return $this->applyFilters($herokuAppThemes, $herokuAppSearch);
    }

    /**
     * @param array $herokuAppThemes
     * @param HerokuAppSearch $herokuAppSearch
     * @return array
     */
    private function applyFilters(array $herokuAppThemes, HerokuAppSearch $herokuAppSearch): array
    {
        if (!$herokuAppSearch->getIndicatorIds()) {
            return $herokuAppThemes;
        }

        $result = [];

        foreach ($herokuAppThemes as $herokuAppTheme) {
            $matchResult = $this->indicatorFilter->applyFilter($herokuAppTheme, $herokuAppSearch->getIndicatorIds());

            if (is_null($matchResult)) {
                continue;
            }

            $result[] = $matchResult->getDataStructure();

            $herokuAppSearch->setIndicatorIds(array_diff($herokuAppSearch->getIndicatorIds(), $matchResult->getFoundedIndicatorIds()));

            // we removed the founded indicator ids so that we can break the cycle to prevent unnecessary loops
            if (!$herokuAppSearch->getIndicatorIds()) {
                break;
            }
        }

        return $result;
    }
}
