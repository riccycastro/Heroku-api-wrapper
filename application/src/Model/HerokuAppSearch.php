<?php

namespace App\Model;

class HerokuAppSearch
{
    /**
     * @var string[]
     */
    private $indicatorIds = [];

    /**
     * @return string[]
     */
    public function getIndicatorIds(): array
    {
        return $this->indicatorIds;
    }

    /**
     * @param array $indicatorIds
     */
    public function setIndicatorIds(array $indicatorIds)
    {
        $this->indicatorIds = $indicatorIds;
    }
}
