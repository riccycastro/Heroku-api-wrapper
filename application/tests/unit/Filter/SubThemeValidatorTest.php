<?php

namespace App\Tests\Filter;

use App\Filter\Validator\AbstractValidator;
use App\Filter\Validator\SubThemeValidator;
use App\Model\MatchData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SubThemeValidatorTest extends TestCase
{
    /**
     * @var SubThemeValidator
     */
    private $subThemeValidator;

    /**
     * @var AbstractValidator|MockObject
     */
    private $nextValidator;

    protected function setUp()
    {
        parent::setUp();

        $this->subThemeValidator = new SubThemeValidator();

        $this->nextValidator = $this->getMockBuilder(AbstractValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subThemeValidator->setNextValidator($this->nextValidator);
    }

    public function testApply_ItShouldReturnNullIfSubThemesNotFound()
    {
        $this->assertNull(
            $this->subThemeValidator->apply([], [])
        );
    }

    public function testApply_ItShouldReturnNullIfSubThemesIsNotAnArray()
    {
        $this->assertNull(
            $this->subThemeValidator->apply(['sub_themes' => 'wrongType'], [])
        );
    }

    public function testApply_ItShouldReturnNullIfNextValidatorReturnsNull()
    {
        $this->nextValidator->method('apply')->willReturn(null);

        $this->assertNull(
            $this->subThemeValidator->apply([
                'sub_themes' => [[]]
            ], [])
        );
    }

    public function testApply_itShouldReturnMatchDataInstance()
    {
        $matchData = new MatchData();

        $matchData->setFoundedIndicatorIds([308]);
        $matchData->setDataStructure([
            'id' => 10,
            'categories' => [
                [
                    'id' => 308,
                    'name' => 'Total',
                ]
            ],
            'name' => 'sub_theme Name'
        ]);

        $this->nextValidator->method('apply')->willReturnOnConsecutiveCalls($matchData, null);

        $expectedMatchData = new MatchData();

        $expectedMatchData->setFoundedIndicatorIds([308]);
        $expectedMatchData->setDataStructure([
            'sub_themes' => [
                [
                    'id' => 10,
                    'name' => 'sub_theme Name',
                    'categories' => [
                        [
                            'id' => 308,
                            'name' => 'Total',
                        ]
                    ],
                ],
            ]
        ]);

        $result = $this->subThemeValidator->apply([
            'sub_themes' => [
                [
                    'id' => 10,
                    'name' => 'sub_theme Name',
                    'indicators' => [
                        [
                            'id' => 308,
                            'name' => 'Total',
                        ]
                    ],
                ],
                [
                    'id' => 50,
                    'name' => 'sub theme Name 2',
                    'categories' => [
                        [
                            'id' => 2,
                            'name' => 'category name',
                        ]
                    ],
                ],
            ]
        ], []);

        $this->assertEquals($expectedMatchData, $result);
    }
}
