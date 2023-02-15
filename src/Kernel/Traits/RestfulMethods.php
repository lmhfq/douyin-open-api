<?php

namespace Lmh\DouyinOpenApi\Kernel\Traits;

use Illuminate\Support\Str;

trait RestfulMethods
{
    /**
     * @return string
     * @throws \ReflectionException
     */
    public static function url(): string
    {
        return static::className();
    }


    /**
     * @return string
     * @throws \ReflectionException
     */
    public static function className(): string
    {
        $className = get_called_class();

        $reflectionClass = new \ReflectionClass($className);
        $classes = explode('\\', $reflectionClass->getNamespaceName());
        $classes = array_slice($classes, 3);
        foreach ($classes as $key => $val) {
            $classes[$key] = $key == count($classes) - 1 ? Str::snake($val) : strtolower($val);
        }
        return implode('/', $classes);
    }
}
