<?php
declare(strict_types=1);


namespace Lmh\DouyinOpenApi\Kernel\Providers;


use Lmh\DouyinOpenApi\Kernel\Logger;
use Lmh\DouyinOpenApi\Kernel\ServiceContainer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple
     */
    public function register(Container $pimple)
    {
        $pimple['logger'] = function ($app) {
            /**
             * @var ServiceContainer $app
             */
            return new Logger($app->offsetGet('config'));
        };
    }
}