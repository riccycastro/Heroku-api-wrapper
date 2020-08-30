<?php

namespace App\Filter\Validator;

use App\Model\MatchData;

class IndicatorValidator extends AbstractValidator
{
    public function __construct()
    {
        parent::__construct('indicators');
    }

    /**
     * @param array $data data here is category array item
     * @param array $indicatorIds
     * @return MatchData|null
     */
    public function apply(array $data, array $indicatorIds): ?MatchData
    {
        // this->key here have the 'indicators' value that was set in the constructor
        if (!isset($data[$this->key]) || !is_array($data[$this->key])) {
            return null;
        }

        $indicators = array_filter($data[$this->key], function ($indicator) use ($indicatorIds) {
            return in_array($indicator['id'], $indicatorIds);
        });

        if (!$indicators) {
            return null;
        }

        // This is the starting point of the ramification
        // it means that we build our final structure backward
        // first we add the indicators that matched, next the categories, sub-themes...
        $matchData = new MatchData();

        $data[$this->key] = array_values($indicators);
        $matchData->setDataStructure($data);
        $matchData->addFoundedIndicatorIds(array_map(function ($indicator) {
            return $indicator['id'];
        }, $indicators));

        return $matchData;
    }
}
