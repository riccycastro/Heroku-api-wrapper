<?php

namespace App\Tests\Service;

use App\HttpException\InternalServerErrorHttpException;
use App\Service\Factory\HttpClientFactory;
use App\Service\HerokuAppClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HerokuAppClientTest extends TestCase
{
    /**
     * @var HerokuAppClient
     */
    private $herokuAppClient;

    /**
     * @var Client|MockObject
     */
    private $httpClient;

    protected function setUp()
    {
        parent::setUp();

        /** @var HttpClientFactory|MockObject $httpClientFactory */
        $httpClientFactory = $this->getMockBuilder(HttpClientFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Container|MockObject $container */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClientFactory->method('makeHttpClient')->willReturn($this->httpClient);
        $container->method('getParameter')->willReturn('tests/phake/path/to/store/location');

        $this->herokuAppClient = new HerokuAppClient(
            'http:\\\\client.phake-domain.local',
            $httpClientFactory,
            $container
        );
    }

    public function testHandleGetData_ItShouldThrowNotFoundException()
    {
        $this->expectException(NotFoundHttpException::class);

        $guzzleRequest = new Request('Get', 'phake/uri');
        $guzzleResponse = new Response(404);
        $guzzleException404 = new BadResponseException('', $guzzleRequest, $guzzleResponse);

        $this->httpClient->method('request')->willThrowException($guzzleException404);

        $this->herokuAppClient->handleGetData('');
    }

    public function testHandleGetData_ItShouldThrowInternalServerErrorAfterMaxAttemptsReached()
    {
        $this->expectException(InternalServerErrorHttpException::class);

        $guzzleResponse = new Response(500);

        $this->httpClient
            ->expects($this->exactly(3))
            ->method('request')
            ->willReturn($guzzleResponse);

        $this->herokuAppClient->handleGetData('');
    }

    public function testHandleGetData_ItShouldThrowInternalServerErrorIfNotRightResponseStructure()
    {
        $this->expectException(InternalServerErrorHttpException::class);

        // we need to do this because when we call the method getContents from response, it erases the content
        $guzzleResponse1 = new Response(
            200,
            [],
            '{"status_code": 500,"detail": "Infernal Server Error.","headers": null}'
        );
        $guzzleResponse2 = new Response(
            200,
            [],
            '{"status_code": 500,"detail": "Infernal Server Error.","headers": null}'
        );
        $guzzleResponse3 = new Response(
            200,
            [],
            '{"status_code": 500,"detail": "Infernal Server Error.","headers": null}'
        );

        $this->httpClient
            ->expects($this->exactly(3))
            ->method('request')
            ->willReturnOnConsecutiveCalls($guzzleResponse1, $guzzleResponse2, $guzzleResponse3);

        $this->herokuAppClient->handleGetData('');
    }

    public function testHandleGetData_ItShouldReturnExpectedResult()
    {
        $guzzleResponse = new Response(
            200,
            [],
            '[
              {
                "id": 2,
                "name": "Demographics",
                "sub_themes": [
                  {
                    "categories": [
                      {
                        "id": 11,
                        "indicators": [
                          {
                            "id": 1,
                            "name": "total"
                          }
                        ],
                        "name": "Crude death rate",
                        "unit": "(deaths per 1000 people)"
                      }
                    ],
                    "id": 4,
                    "name": "Births and Deaths"
                  }
                ]
              }
             ]'
        );

        $this->httpClient
            ->expects($this->exactly(1))
            ->method('request')
            ->willReturn($guzzleResponse);

        $expectedResult = [
            [
                'id' => 2,
                'name' => 'Demographics',
                'sub_themes' => [
                    [
                        'categories' => [
                            [
                                'id' => 11,
                                'indicators' => [
                                    [
                                        'id' => 1,
                                        'name' => 'total'
                                    ]
                                ],
                                'name' => 'Crude death rate',
                                'unit' => '(deaths per 1000 people)'
                            ]
                        ],
                        'id' => 4,
                        'name' => 'Births and Deaths'
                    ]
                ]
            ],
        ];

        $this->assertEquals($expectedResult, $this->herokuAppClient->handleGetData(''));
    }
}
