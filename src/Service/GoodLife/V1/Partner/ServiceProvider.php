<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2022/6/9
 * Time: 下午3:23
 */

namespace Lmh\DouyinOpenApi\Service\GoodLife\V1\Partner;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['transfer'] = function ($app) {
            return new Client($app);
        };
    }
}