<?php

namespace VeripayTT\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use VeripayTT\SDK\Resources\PaymentLinks;
use VeripayTT\SDK\Resources\Payments;
use VeripayTT\SDK\Exceptions\VeriPayException;

/**
 * VeriPay API Client
 * 
 * This is the main client class for interacting with the VeriPay API.
 * It handles authentication, HTTP requests with retry logic, and provides
 * access to various API resource endpoints.
 * 
 * @package VeripayTT\SDK
 */
class VeriPayClient
{
    /** @var string The API key used for authentication */
    protected string $apiKey;

    /** @var Client The Guzzle HTTP client instance */
    protected Client $httpClient;

    /** @var array Configuration options for the client */
    protected array $config;

    /** @var LoggerInterface|null Optional logger instance for request logging */
    protected ?LoggerInterface $logger;

    /**
     * Constructor for VeriPayClient
     * 
     * Initializes the client with API key, configuration options, and optional logger.
     * Sets up the HTTP client with authentication headers and default configuration.
     * 
     * @param string $apiKey The VeriPay API key for authentication
     * @param array $config Optional configuration array to override defaults
     * @param LoggerInterface|null $logger Optional PSR-3 logger for request logging
     */
    public function __construct(string $apiKey, array $config = [], LoggerInterface $logger = null)
    {
        $this->apiKey = $apiKey;
        $this->logger = $logger;

        $this->config = array_merge([
            'base_urls' => [
                'production' => 'https://api.veripay.us',
                'staging'    => 'https://staging-api.veripay.us',
                'sandbox'    => 'https://sandbox-api.veripay.us',
            ],
            'environment' => 'production',
            'log_requests' => false,
            'retry_times' => 3,
            'retry_delay' => 100,
        ], $config);

        if (!isset($this->config['base_url'])) {
            $env = $this->config['environment'];
            $this->config['base_url'] = $this->config['base_urls'][$env] ?? $this->config['base_urls']['production'];
        }

        $this->httpClient = new Client([
            'base_uri' => $this->config['base_url'],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Make an HTTP request to the VeriPay API with retry logic
     * 
     * This method handles all HTTP communication with the VeriPay API.
     * It includes automatic retry logic for failed requests and optional
     * request/response logging.
     * 
     * @param string $method HTTP method (GET, POST, PUT, DELETE, etc.)
     * @param string $uri The API endpoint URI (relative to base URL)
     * @param array $options Guzzle request options (headers, body, query params, etc.)
     * @return array The decoded JSON response as an associative array
     * @throws VeriPayException If all retry attempts fail
     */
    public function request(string $method, string $uri, array $options = [])
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts < $this->config['retry_times']) {
            try {
                $response = $this->httpClient->request($method, $uri, $options);

                if ($this->config['log_requests'] && $this->logger) {
                    $this->logger->info('[VeriPay] Request', [
                        'method' => $method,
                        'uri' => $uri,
                        'options' => $options,
                        'status' => $response->getStatusCode(),
                        'body' => (string) $response->getBody()
                    ]);
                }

                return json_decode((string) $response->getBody(), true);
            } catch (RequestException $e) {
                $lastException = $e;

                if ($this->config['log_requests'] && $this->logger) {
                    $this->logger->error('[VeriPay] Request failed', [
                        'method' => $method,
                        'uri' => $uri,
                        'error' => $e->getMessage()
                    ]);
                }

                usleep($this->config['retry_delay'] * 1000);
                $attempts++;
            }
        }

        throw new VeriPayException('VeriPay request failed after retries.', 0, $lastException);
    }

    /**
     * Get the PaymentLinks resource handler
     * 
     * Provides access to payment link related API endpoints such as
     * creating, retrieving, updating, and deleting payment links.
     * 
     * @return PaymentLinks The payment links resource handler
     */
    public function paymentLinks(): PaymentLinks
    {
        return new PaymentLinks($this);
    }

    /**
     * Get the Payments resource handler
     * 
     * Provides access to payment related API endpoints such as
     * retrieving payment status
     * 
     * @return Payments The payments resource handler
     */
    public function payments(): Payments
    {
        return new Payments($this);
    }

    /**
     * Static factory method to create a VeriPayClient instance
     * 
     * Convenience method to create a new client instance with the provided
     * API key and optional configuration. This is an alternative to using
     * the constructor directly.
     * 
     * @param string $apiKey The VeriPay API key for authentication
     * @param array $config Optional configuration array to override defaults
     * @param LoggerInterface|null $logger Optional PSR-3 logger for request logging
     * @return self A new VeriPayClient instance
     */
    public static function makeWithKey(string $apiKey, array $config = [], ?LoggerInterface $logger = null): self
    {
        return new self($apiKey, $config, $logger);
    }
}
