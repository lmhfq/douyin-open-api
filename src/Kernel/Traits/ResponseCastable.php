<?php

namespace Lmh\DouyinOpenApi\Kernel\Traits;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Lmh\DouyinOpenApi\Kernel\Contracts\Arrayable;
use Lmh\DouyinOpenApi\Kernel\Exceptions\InvalidConfigException;
use Lmh\DouyinOpenApi\Kernel\Support\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait ResponseCastable.
 *
 * @author overtrue <i@overtrue.me>
 */
trait ResponseCastable
{
    /**
     * @param mixed $response
     * @param string|null $type
     *
     * @throws InvalidConfigException
     */
    protected function detectAndCastResponseToType($response, string $type = null)
    {
        switch (true) {
            case $response instanceof ResponseInterface:
                $response = Response::buildFromPsrResponse($response);

                break;
            case ($response instanceof Collection) || is_array($response) || is_object($response):
                $response = new Response(200, [], json_encode($response));

                break;
            case is_scalar($response):
                $response = new Response(200, [], $response);

                break;
            default:
                throw new InvalidArgumentException(sprintf('Unsupported response type "%s"', gettype($response)));
        }

        return $this->castResponseToType($response, $type);
    }

    /**
     * @param ResponseInterface $response
     * @param string|null $type
     *
     * @return mixed
     * @throws InvalidConfigException
     */
    protected function castResponseToType(ResponseInterface $response, string $type = null)
    {
        $response = Response::buildFromPsrResponse($response);
        $response->getBody()->rewind();
        switch ($type ?? 'array') {
            case 'collection':
                return $response->toCollection();
            case 'array':
                return $response->toArray();
            case 'object':
                return $response->toObject();
            case 'raw':
                return $response;
            default:
                if (!is_subclass_of($type, Arrayable::class)) {
                    throw new InvalidConfigException(sprintf('Config key "response_type" classname must be an instanceof %s', Arrayable::class));
                }
                return new $type($response);
        }
    }
}
