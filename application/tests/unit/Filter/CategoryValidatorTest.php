<?php

namespace App\Tests\Filter;

use App\Filter\Validator\AbstractValidator;
use App\Filter\Validator\CategoryValidator;
use App\Model\MatchData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CategoryValidatorTest extends TestCase
{
    /**
     * @var CategoryValidator
     */
    private $categoryValidator;

    /**
     * @var AbstractValidator|MockObject
     */
    private $nextValidator;

    protected function setUp()
    {
        parent::setUp();

        $this->categoryValidator = new CategoryValidator();

        $this->nextValidator = $this->getMockBuilder(AbstractValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryValidator->setNextValidator($this->nextValidator);
    }

    public function testApply_ItShouldReturnNullIfCategoriesNotFound()
    {
        $this->assertNull(
            $this->categoryValidator->apply([], [])
        );
    }

    public function testApply_ItShouldReturnNullIfCategoriesIsNotAnArray()
    {
        $this->assertNull(
            $this->categoryValidator->apply(['categories' => 'wrongType'], [])
        );
    }

    public function testApply_ItShouldReturnNullIfNextValidatorReturnsNull()
    {
        $this->nextValidator->method('apply')->willReturn(null);

        $this->assertNull(
            $this->categoryValidator->apply([
                'categories' => [[]]
            ], [])
        );
    }

    public function testApply_itShouldReturnMatchDataInstance()
    {
        $matchData = new MatchData();

        $matchData->setFoundedIndicatorIds([308]);
        $matchData->setDataStructure([
            'id' => 10,
            'indicators' => [
                [
                    'id' => 308,
                    'name' => 'Total',
                ]
            ],
            'name' => 'Category Name'
        ]);

        $this->nextValidator->method('apply')->willReturnOnConsecutiveCalls($matchData, null);

        $expectedMatchData = new MatchData();

        $expectedMatchData->setFoundedIndicatorIds([308]);
        $expectedMatchData->setDataStructure([
            'categories' => [
                [
                    'id' => 10,
                    'name' => 'Category Name',
                    'indicators' => [
                        [
                            'id' => 308,
                            'name' => 'Total',
                        ]
                    ],
                ],
            ]
        ]);

        $result = $this->categoryValidator->apply([
            'categories' => [
                [
                    'id' => 10,
                    'name' => 'Category Name',
                    'indicators' => [
                        [
                            'id' => 308,
                            'name' => 'Total',
                        ]
                    ],
                ],
                [
                    'id' => 50,
                    'name' => 'Category Name 2',
                    'indicators' => [
                        [
                            'id' => 2,
                            'name' => 'indicator name',
                        ]
                    ],
                ],
            ]
        ], []);

        $this->assertEquals($expectedMatchData, $result);
    }
}
