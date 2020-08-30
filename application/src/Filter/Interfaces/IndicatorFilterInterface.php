<?php

namespace App\Filter\Interfaces;

use App\Model\MatchData;

interface IndicatorFilterInterface
{
    /**
     * @param array $theme
     * @param array $indicatorIds
     * @return MatchData|null
     */
    public function applyFilter(array $theme, array $indicatorIds): ?MatchData;
}
