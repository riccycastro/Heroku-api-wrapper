<?php

namespace App\Filter\Validator;

use App\Model\MatchData;

abstract class AbstractValidator
{
    /**
     * @var AbstractValidator
     */
    protected $nextValidator;

    /**
     * @var string
     */
    protected $key;

    /**
     * AbstractValidator constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param array $data data here can be the theme or sub_theme array
     * @param array $indicatorIds
     * @return MatchData|null
     */
    public function apply(array $data, array $indicatorIds): ?MatchData
    {
        if (!isset($data[$this->key]) || !is_array($data[$this->key])) {
            return null;
        }

        /**
         * since we want to keep the same properties in our result, here we are copying
         * the original structure but resetting the property that we are validating
         * this validated property should have only the values that matched the requested
         * indicators
         */
        $dataStructure = $data;
        $dataStructure[$this->key] = [];
        $foundedIndicatorsIds = [];

        foreach ($data[$this->key] as $item) {
            // we pass our sub_theme or category array to the next validator
            $matchDataReturned = $this->nextValidator->apply($item, $indicatorIds);

            if (!$matchDataReturned) {
                continue;
            }

            // since each sub_theme or category can have different matches in the search, we need to
            // merge them in one result
            $foundedIndicatorsIds = array_merge($foundedIndicatorsIds, $matchDataReturned->getFoundedIndicatorIds());

            // since we are building our result backwards, we add to the current level the result from the above
            // and return it to be added to the level above
            $dataStructure[$this->key][] = $matchDataReturned->getDataStructure();
        }

        if (!$foundedIndicatorsIds || !$dataStructure[$this->key]) {
            return null;
        }


        $matchResult = new MatchData();
        $matchResult->setDataStructure($dataStructure);
        $matchResult->setFoundedIndicatorIds($foundedIndicatorsIds);
        return $matchResult;
    }

    /**
     * Characteristic of the Chain Of Responsibility pattern
     *
     * @param AbstractValidator $nextValidator
     */
    public function setNextValidator(AbstractValidator $nextValidator): void
    {
        $this->nextValidator = $nextValidator;
    }
}
