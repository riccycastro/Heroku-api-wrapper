<?php

namespace App\Tests\Service;

use App\Filter\IndicatorFilter;
use App\Filter\MatchData;
use App\Model\HerokuAppSearch;
use App\Service\DemographicService;
use App\Service\HerokuAppClient;
use App\Tests\Helpers\PayloadReaderTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DemographicServiceTest extends TestCase
{
    /**
     * @var DemographicService
     */
    private $demographicService;

    /**
     * @var HerokuAppClient|MockObject
     */
    private $herokuAppClient;

    /**
     * @var IndicatorFilter|MockObject
     */
    private $indicatorFilter;

    protected function setUp()
    {
        parent::setUp();

        $this->herokuAppClient = $this->getMockBuilder(HerokuAppClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->indicatorFilter = $this->getMockBuilder(IndicatorFilter::class)
            ->getMock();

        $this->demographicService = new DemographicService(
            $this->herokuAppClient,
            $this->indicatorFilter
        );
    }

    public function testGetData_ItShouldHerokuAppThemesWithoutFilter()
    {
        $herokuAppSearch = new HerokuAppSearch();
        $this->setHandleGetDataReturn();

        $this->assertEquals(
            PayloadReaderTest::loadHerokuResponseData(),
            $this->demographicService->getData('', $herokuAppSearch)
        );
    }

    public function testGetData_ItShouldReturnEmptyIfNoMatchResult()
    {
        $this->indicatorFilter->method('applyFilter')->willReturn(null);
        $herokuAppSearch = new HerokuAppSearch();
        $herokuAppSearch->setIndicatorIds([1000]);
        $this->setHandleGetDataReturn();

        $this->assertEquals(
            [],
            $this->demographicService->getData('', $herokuAppSearch)
        );
    }

    public function testGetData_ItShouldReturnExpectedResult()
    {
        $expectedResult = [
            "id" => 1,
            "name" => "Urban Extent",
            "sub_themes" => [
                [
                    "categories" => [
                        [
                            "id" => 1,
                            "indicators" => [
                                [
                                    "id" => 299,
                                    "name" => "Total"
                                ]
                            ],
                            "name" => "Area",
                            "unit" => "(sq. km.)"
                        ],
                    ]
                ]
            ]
        ];

        $matchData = new MatchData();
        $matchData->setDataStructure($expectedResult);
        $matchData->setFoundedIndicatorIds([299]);

        $this->indicatorFilter->method('applyFilter')->willReturnOnConsecutiveCalls(
            $matchData
        );

        $herokuAppSearch = new HerokuAppSearch();
        $herokuAppSearch->setIndicatorIds([299]);
        $this->setHandleGetDataReturn();

        $this->assertEquals(
            [$expectedResult],
            $this->demographicService->getData('', $herokuAppSearch)
        );
    }

    private function setHandleGetDataReturn()
    {
        $this->herokuAppClient->method('handleGetData')->willReturn(
            PayloadReaderTest::loadHerokuResponseData()
        );
    }
}
