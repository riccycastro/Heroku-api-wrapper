<?php

namespace App\Filter;

use App\Filter\Interfaces\IndicatorFilterInterface;

class IndicatorFilter implements IndicatorFilterInterface
{
    /**
     * @inheritDoc
     */
    public function applyFilter(array $theme, array $indicatorIds): ?MatchData
    {
        $categoryValidator = new CategoryValidator();
        $subThemeValidator = new SubThemeValidator();
        $indicatorValidator = new IndicatorValidator();

        $categoryValidator->setNextValidator($indicatorValidator);
        $subThemeValidator->setNextValidator($categoryValidator);

        return $subThemeValidator->apply($theme, $indicatorIds);
    }
}
