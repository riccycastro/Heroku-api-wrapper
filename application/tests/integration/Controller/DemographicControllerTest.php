<?php

use App\Service\Factory\HttpClientFactory;
use App\Tests\Helpers\PayloadReaderTest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemographicControllerTest extends WebTestCase
{

    /**
     * @var KernelBrowser
     */
    private $testClient;

    /**
     * @var Client|MockObject
     */
    private $httpClient;

    protected function setUp()
    {
        parent::setUp();

        $this->testClient = static::createClient();

        /** @var HttpClientFactory|MockObject $httpClientFactory */
        $httpClientFactory = $this->getMockBuilder(HttpClientFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClientFactory->method('makeHttpClient')->willReturn($this->httpClient);

        $this->testClient->getContainer()->set('factory.client.http', $httpClientFactory);
        $this->testClient->setServerParameter('cache_store_location', '3');
    }


    public function testIndexAction_ItShouldIgnoreNotValidIndicatorIdsParam()
    {
        $guzzleResponse = new Response(
            200,
            [],
            PayloadReaderTest::loadHerokuResponse()
        );

        $this->httpClient->method('request')->willReturn($guzzleResponse);

        $this->testClient->request('GET', '/demographics/data?indicator_ids=2');

        $this->assertEquals(200, $this->testClient->getResponse()->getStatusCode());
        $this->assertEquals(
            PayloadReaderTest::loadHerokuResponseArray(),
            json_decode($this->testClient->getResponse()->getContent(), true)
        );
    }

    public function testIndexAction_ItShouldApplyFilter()
    {
        $guzzleResponse = new Response(
            200,
            [],
            PayloadReaderTest::loadHerokuResponse()
        );

        $this->httpClient->method('request')->willReturn($guzzleResponse);

        $this->testClient->request('GET', '/demographics/data?indicator_ids[]=31&indicator_ids[]=32&indicator_ids[]=1&indicator_ids[]=362');

        $this->assertEquals(200, $this->testClient->getResponse()->getStatusCode());
        $this->assertEquals(
            PayloadReaderTest::loadHerokuResponseFilterArray(),
            json_decode($this->testClient->getResponse()->getContent(), true)
        );
    }

    public function testIndexAction_ItShouldReturnStatus404()
    {
        $guzzleRequest = new Request('Get', 'phake/uri');
        $guzzleResponse = new Response(404);
        $guzzleException404 = new BadResponseException('', $guzzleRequest, $guzzleResponse);

        $this->httpClient->method('request')->willThrowException($guzzleException404);

        $this->testClient->request('GET', '/demographics/cenas');

        $this->assertEquals(404, $this->testClient->getResponse()->getStatusCode());

        $this->assertEquals(
            json_decode('{"status_code": 404,"detail": "Route not found for \"\/cenas\""}', true),
            json_decode($this->testClient->getResponse()->getContent(), true)
        );
    }

    public function testIndexAction_ItShouldThrowInternalServerErrorIfNotRightResponseStructure()
    {
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

        $this->testClient->request('GET', '/demographics/data');

        $this->assertEquals(500, $this->testClient->getResponse()->getStatusCode());

    }
}
