<?php

namespace App\Service;

use App\HttpException\InternalServerErrorHttpException;
use App\Service\Factory\HttpClientFactory;
use App\Service\Interfaces\HerokuAppClientInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HerokuAppClient implements HerokuAppClientInterface
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $cacheStoreLocation;

    /**
     * @var int
     */
    private $maxAttempts = 3;

    /**
     * HerokuAppClient constructor.
     * @param string $herokuAppDomain
     * @param HttpClientFactory $clientFactory
     * @param ContainerInterface $container
     */
    public function __construct(
        string $herokuAppDomain,
        HttpClientFactory $clientFactory,
        ContainerInterface $container
    )
    {
        $this->domain = $herokuAppDomain;
        $this->cacheStoreLocation = $container->getParameter('cache_store_location');
        $this->client = $clientFactory->makeHttpClient(HttpClientFactory::HTTP_CLIENT_WITH_CACHE);
    }

    /**
     * @inheritDoc
     */
    public function handleGetData(string $endpointName): array
    {
        $attempts = 0;

        while ($attempts < $this->maxAttempts) {
            $attempts++;
            $response = null;

            try {
                $response = $this->getData($endpointName);
            } catch (GuzzleException $exception) {
                if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                    throw new NotFoundHttpException("Route not found for \"/$endpointName\"");
                }
            }
            if ($response->getStatusCode() === Response::HTTP_OK) {
                try {
                    $responseData = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

                    if (!isset($responseData["status_code"]) && is_array($responseData)) {
                        return $responseData;
                    }

                    // at this stage if the response has the header age
                    // it means that we have cached an "infernal server error"
                    // so we need to clear it
                    if ($response->hasHeader('age')) {
                        $this->clearCache();
                    }

                } catch (Exception $e) {
                    throw new InternalServerErrorHttpException();
                }
            }
        }

        $this->clearCache();
        throw new InternalServerErrorHttpException();
    }

    /**
     * @param string $endpointName
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function getData(string $endpointName): ResponseInterface
    {
        return $this->client->request(
            'GET',
            "$this->domain/$endpointName"
        );

    }

    private function clearCache(): void
    {
        $fileSystemAdapter = new FilesystemAdapter('',
            0,
            $this->cacheStoreLocation);
        $fileSystemAdapter->clear();
    }
}
