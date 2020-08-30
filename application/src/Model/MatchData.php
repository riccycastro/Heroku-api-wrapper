<?php

namespace App\Model;

/**
 * Class MatchData
 * @package App\Filter
 *
 * A helper class to keep track of the ramifications that have
 * the indicators requested
 */
class MatchData
{
    /**
     * @var array
     */
    private $foundedIndicatorIds = [];

    /**
     * @var array
     */
    private $dataStructure = [];

    /**
     * @return array
     */
    public function getFoundedIndicatorIds(): array
    {
        return $this->foundedIndicatorIds;
    }

    /**
     * @param array $foundedIndicatorIds
     */
    public function setFoundedIndicatorIds(array $foundedIndicatorIds): void
    {
        $this->foundedIndicatorIds = $foundedIndicatorIds;
    }

    public function addFoundedIndicatorIds(array $indicatorsToAdd): void
    {
        $this->foundedIndicatorIds = array_merge($this->foundedIndicatorIds, $indicatorsToAdd);
    }

    /**
     * @param array $dataStructure
     */
    public function setDataStructure(array $dataStructure): void
    {
        $this->dataStructure = $dataStructure;
    }

    /**
     * @return array
     */
    public function getDataStructure(): array
    {
        return $this->dataStructure;
    }
}
