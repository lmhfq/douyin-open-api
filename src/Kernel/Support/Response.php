<?php


namespace Lmh\DouyinOpenApi\Kernel\Support;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use function preg_replace;

/**
 * Class Response.
 *
 * @author overtrue <i@overtrue.me>
 */
class Response extends GuzzleResponse
{
    /**
     * @param ResponseInterface $response
     *
     * @return Response
     */
    public static function buildFromPsrResponse(ResponseInterface $response): Response
    {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    /**
     * Get collection data.
     *
     */
    public function toCollection(): Collection
    {
        return new Collection($this->toArray());
    }

    /**
     * Build to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $content = $this->removeControlCharacters($this->getBodyContents());

        if (false !== stripos($this->getHeaderLine('Content-Type'), 'xml') || 0 === stripos($content, '<xml')) {
            return XML::parse($content);
        }

        $array = json_decode($content, true);

        if (JSON_ERROR_NONE === json_last_error()) {
            return (array)$array;
        }

        return [];
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function removeControlCharacters(string $content): string
    {
        return preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $content);
    }

    /**
     * @return string
     */
    public function getBodyContents()
    {
        $this->getBody()->rewind();
        $contents = $this->getBody()->getContents();
        $this->getBody()->rewind();

        return $contents;
    }

    /**
     * @return object
     */
    public function toObject()
    {
        return json_decode($this->toJson());
    }

    /**
     * Build to json.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getBodyContents();
    }
}
