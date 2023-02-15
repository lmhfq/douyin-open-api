<?php
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/2/15
 * Time: 14:20
 */

namespace Lmh\DouyinOpenApi\Kernel\Traits;

use Psr\Http\Message\RequestInterface;

trait SignatureGenerator
{
    /**
     * 生成请求需要的头部签名
     * @param RequestInterface $request
     * @param array $options
     * @return string
     * @see https://developer.open-douyin.com/docs/resource/zh-CN/dop/develop/openapi/life-service-open-ability/life.capacity/beforeinsert/signruleintroduce/
     */
    protected function signHeader(RequestInterface $request, array $options): string
    {
        parse_str($request->getUri()->getQuery(), $query);
        $payload = [
            'client_secret' => $this->app['config']['app_secret'],
            'timestamp' => microtime(true) / 1000,
        ];
        $payload = array_merge($payload, $query);
        ksort($payload);
        $signStr = http_build_query($payload);
        if ($request->getMethod() === 'POST') {
            $signStr .= '&http_body=' . $request->getBody()->getContents();
        }
        return hash('sha256', $signStr);
    }
}