<?php

namespace Lmh\DouyinOpenApi\Kernel\Providers;

use GuzzleHttp\Client;
use Lmh\DouyinOpenApi\Kernel\ServiceContainer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class HttpClientServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        !isset($pimple['http_client']) && $pimple['http_client'] = function ($app) {
            /**
             * @var ServiceContainer $app
             */
            return new Client($app['config']->get('http', []));
        };
    }
}
