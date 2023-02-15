<?php

namespace Lmh\DouyinOpenApi\Kernel\Providers;

use Lmh\DouyinOpenApi\Kernel\Config;
use Lmh\DouyinOpenApi\Kernel\ServiceContainer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        !isset($pimple['config']) && $pimple['config'] = function ($app) {
            /**
             * @var ServiceContainer $app
             */
            return new Config($app->getConfig());
        };
    }
}
