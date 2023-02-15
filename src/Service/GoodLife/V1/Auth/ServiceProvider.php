<?php
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2022/12/20
 * Time: 16:25
 */

namespace Lmh\DouyinOpenApi\Service\GoodLife\V1\Auth;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple)
    {
        !isset($pimple['access_token']) && $pimple['access_token'] = function ($pimple) {
            return new AccessToken($pimple);
        };
    }
}