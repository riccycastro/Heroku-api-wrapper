<?php

namespace App\Service\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use http\Exception\RuntimeException;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HttpClientFactory
{
    const HTTP_CLIENT_WITH_CACHE = 'http_client_cache';
    const HTTP_CLIENT = 'http_client';

    /**
     * @var string
     */
    private $cacheStoreLocation;

    /**
     * @var string
     */
    private $herokuAppClientCacheTTL;

    public function __construct(ContainerInterface $container, string $herokuAppClientCacheTTL)
    {
        $this->cacheStoreLocation = $container->getParameter('cache_store_location');
        $this->herokuAppClientCacheTTL = $herokuAppClientCacheTTL;
    }

    /**
     * @param string $httpClientType
     * @return Client
     */
    public function makeHttpClient(string $httpClientType): ClientInterface
    {
        switch ($httpClientType) {
            case self::HTTP_CLIENT_WITH_CACHE:
                return $this->generateClientWithCache();
            case self::HTTP_CLIENT:
                return new Client();
            default:
                throw new RuntimeException('Not a valid http client type!');
        }
    }

    private function generateClientWithCache(): ClientInterface
    {
        $stack = HandlerStack::create();


        $cache_storage = new Psr6CacheStorage(
            new FilesystemAdapter(
                '',
                $this->herokuAppClientCacheTTL,
                $this->cacheStoreLocation
            )
        );

        // Add Cache Method
        $stack->push(
            new CacheMiddleware(
                new GreedyCacheStrategy(
                    $cache_storage,
                    $this->herokuAppClientCacheTTL
                )
            ),
            'http-client-cache'
        );

        // Initialize the client with the cache handler
        return new Client(['handler' => $stack]);
    }
}
