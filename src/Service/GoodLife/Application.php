<?php
namespace Lmh\DouyinOpenApi\Service\GoodLife;
use Lmh\DouyinOpenApi\Kernel\ServiceContainer;

/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * @property Shop\Client $shop
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Auth\ServiceProvider::class,
        Shop\ServiceProvider::class,
    ];
}