<?php

namespace Lmh\DouyinOpenApi;


use Illuminate\Support\Str;
use Lmh\DouyinOpenApi\Kernel\ServiceContainer;
use Lmh\DouyinOpenApi\Service\GoodLife\Application;

/**
 * @method static Application    goodLife(array $config)
 */
class Factory
{
    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return self::make($name, ...$arguments);
    }

    /**
     * @param string $name
     * @param array $config
     * @return mixed
     */
    public static function make(string $name, array $config): ServiceContainer
    {
        $namespace = Str::studly($name);
        $application = "\\Lmh\\DouyinOpenApi\\Service\\{$namespace}\\Application";
        return new $application($config);
    }
}