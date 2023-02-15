<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2022/6/9
 * Time: 下午3:23
 */

namespace Lmh\DouyinOpenApi\Service\GoodLife\Shop;


use GuzzleHttp\Exception\GuzzleException;
use Lmh\DouyinOpenApi\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param array $params
     * @return array
     * @throws GuzzleException
     * @author lmh
     */
    public function query(array $params): array
    {
        $url = self::classUrl();
        return $this->httpGet($url, $params);
    }
}