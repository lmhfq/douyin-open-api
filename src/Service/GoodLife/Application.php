<?php

namespace Lmh\DouyinOpenApi\Service\GoodLife;

use Lmh\DouyinOpenApi\Kernel\ServiceContainer;

/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * @property V1\Shop\Client $shop
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        V1\Auth\ServiceProvider::class,
        V1\Shop\ServiceProvider::class,
    ];
}