<?php

namespace Lmh\DouyinOpenApi\Kernel;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Lmh\DouyinOpenApi\Kernel\Contracts\AccessTokenInterface;
use Lmh\DouyinOpenApi\Kernel\Support\Response;
use Lmh\DouyinOpenApi\Kernel\Traits\HasHttpRequests;
use Lmh\DouyinOpenApi\Kernel\Traits\RestfulMethods;
use Lmh\DouyinOpenApi\Kernel\Traits\SignatureGenerator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

class BaseClient
{

    use RestfulMethods, HasHttpRequests, SignatureGenerator {
        request as performRequest;
    }

    /**
     * @var ServiceContainer
     */
    protected $app;
    /**
     * @var AccessTokenInterface|null
     */
    protected $accessToken = null;
    /**
     * @var string
     */
    protected $baseUri;

    /**
     * BaseClient constructor.
     *
     */
    public function __construct(ServiceContainer $app, AccessTokenInterface $accessToken = null)
    {
        $this->app = $app;
        $this->accessToken = $accessToken ?? $this->app['access_token'];
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array $query
     *
     * @throws GuzzleException
     */
    public function httpGet(string $url, array $query = [])
    {
        return $this->request($url, 'GET', ['query' => $query]);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $options
     * @param bool $returnRaw
     *
     * @return mixed|ResponseInterface
     * @throws GuzzleException
     */
    public function request(string $url, string $method = 'GET', array $options = [], bool $returnRaw = false)
    {
        if (empty($this->middlewares)) {
            $this->registerHttpMiddlewares();
        }
        $response = $this->performRequest($url, $method, $options);
        return $returnRaw ? $response : $this->castResponseToType($response, $this->app->config->get('response_type'));
    }

    /**
     * Register Guzzle middlewares.
     */
    protected function registerHttpMiddlewares()
    {
        // retry
        $this->pushMiddleware($this->retryMiddleware(), 'retry');
        // access token
        $this->pushMiddleware($this->accessTokenMiddleware(), 'access_token');
        // SPI sign
        $this->pushMiddleware($this->signMiddleware(), 'sign');
        // log
        $this->pushMiddleware($this->logMiddleware(), 'log');
    }

    /**
     * Return retry middleware.
     *
     * @return Closure
     */
    protected function retryMiddleware(): Closure
    {
        return Middleware::retry(
            function (
                $retries,
                RequestInterface $request,
                ResponseInterface $response = null
            ) {
                // Limit the number of retries to 2
                if ($retries < $this->app->config->get('http.max_retries', 1) && $response && $body = $response->getBody()) {
                    // Retry on server errors
                    $response = json_decode($body, true);
                    if (!empty($response['errcode']) && in_array(abs($response['errcode']), [40001, 40014, 42001], true)) {
                        $this->accessToken->refresh();
                        $this->app['logger']->debug('Retrying with refreshed access token.');
                        return true;
                    }
                }
                return false;
            },
            function () {
                return abs($this->app->config->get('http.retry_delay', 500));
            }
        );
    }

    /**
     * Attache access token to request query.
     *
     * @return Closure
     */
    protected function accessTokenMiddleware(): Closure
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if ($this->accessToken) {
                    $request = $this->accessToken->applyToRequest($request, $options);
                }
                return $handler($request, $options);
            };
        };
    }

    /**
     * Attache auth to the request header.
     *
     * @return Closure
     */
    protected function signMiddleware(): Closure
    {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array            $options
            ) use ($handler) {
                $request = $request->withHeader('Accept', 'application/json');
                $request = $request->withHeader('x-life-clientkey', $this->app['config']['app_id']);
                $request = $request->withHeader('x-life-sign', $this->signHeader($request, $options));
                return $handler($request, $options);
            };
        };
    }

    /**
     * Log the request.
     *
     * @return Closure
     */
    protected function logMiddleware(): Closure
    {

        $formatter = new MessageFormatter($this->app['config']['http.log_template'] ?? MessageFormatter::DEBUG);
        return Middleware::log($this->app['logger'], $formatter, LogLevel::DEBUG);
    }

    /**
     * POST request.
     *
     * @param string $url
     * @param array $data
     *
     */
    public function httpPost(string $url, array $data = [])
    {
        return $this->request($url, 'POST', ['form_params' => $data]);
    }

    /**
     * JSON request.
     *
     * @param string $url
     * @param array $data
     * @param array $query
     *
     */
    public function httpPostJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }

    /**
     * Upload file.
     *
     * @param string $url
     * @param array $files
     * @param array $form
     * @param array $query
     * @return mixed|ResponseInterface
     */
    public function httpUpload(string $url, array $files = [], array $form = [], array $query = [])
    {
        $multipart = [];

        foreach ($files as $name => $path) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path, 'r'),
            ];
        }

        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        return $this->request(
            $url,
            'POST',
            ['query' => $query, 'multipart' => $multipart, 'connect_timeout' => 30, 'timeout' => 30, 'read_timeout' => 30]
        );
    }

    /**
     * @return AccessTokenInterface
     */
    public function getAccessToken(): AccessTokenInterface
    {
        return $this->accessToken;
    }

    /**
     *
     * @return $this
     */
    public function setAccessToken(AccessTokenInterface $accessToken): BaseClient
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $options
     * @return Response
     */
    public function requestRaw(string $url, string $method = 'GET', array $options = []): Response
    {
        return Response::buildFromPsrResponse($this->request($url, $method, $options, true));
    }

    /**
     * Return GuzzleHttp\ClientInterface instance.
     *
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        if (!($this->httpClient instanceof ClientInterface)) {
            $this->httpClient = $this->app['http_client'] ?? new Client();
        }
        return $this->httpClient;
    }
}