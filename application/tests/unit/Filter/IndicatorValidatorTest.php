<?php

namespace App\Tests\Filter;

use App\Filter\IndicatorValidator;
use App\Filter\MatchData;
use PHPUnit\Framework\TestCase;

class IndicatorValidatorTest extends TestCase
{
    /**
     * @var IndicatorValidator
     */
    private $indicatorValidator;

    protected function setUp()
    {
        parent::setUp();

        $this->indicatorValidator = new IndicatorValidator();
    }

    public function testApply_ItShouldReturnNullIfIndicatorsNotFound()
    {
        $this->assertNull(
            $this->indicatorValidator->apply([], [])
        );
    }

    public function testApply_ItShouldReturnNullIfIndicatorsIsNotAnArray()
    {
        $this->assertNull(
            $this->indicatorValidator->apply(['indicators' => 'wrongType'], [])
        );
    }

    public function testApply_ItShouldReturnNullIfNotInIndicatorIds()
    {
        $data = [
            'indicators' => [
                ['id' => 12],
                ['id' => 1],
                ['id' => 5],
            ]
        ];

        $indicatorIds = [2, 3, 4];

        $this->assertNull(
            $this->indicatorValidator->apply($data, $indicatorIds)
        );
    }

    public function testApply_ItShouldReturnMatchData()
    {
        $data = [
            'indicators' => [
                ['id' => 1],
                ['id' => 5],
                ['id' => 7],
                ['id' => 12],
            ]
        ];

        $indicatorIds = [1, 12];

        $matchData = new MatchData();
        $matchData->setDataStructure(['indicators' => [['id' => 1], ['id' => 12]]]);
        $matchData->setFoundedIndicatorIds([1, 12]);

        $this->assertEquals(
            $matchData,
            $this->indicatorValidator->apply($data, $indicatorIds)
        );
    }
}
