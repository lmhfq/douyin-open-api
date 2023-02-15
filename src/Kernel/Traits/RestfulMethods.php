<?php
namespace Lmh\DouyinOpenApi\Kernel\Traits;

use Illuminate\Support\Str;

trait RestfulMethods
{
    /**
     * @param $id
     * @return string
     */
    public function instanceUrl($id): string
    {
        return self::classUrl() . '/' . $id;
    }

    /**
     * @return string
     */
    public static function classUrl(): string
    {
        return '/v1/' . static::className();
    }


    /**
     * @return string
     */
    public static function className(): string
    {
        $className = get_called_class();
        $classes = explode('\\', $className);
        $classes = array_slice($classes, 3, -1);
        foreach ($classes as $key => $val) {
            $classes[$key] = $key == count($classes) - 1 ? Str::plural(Str::snake($val)) : strtolower($val);
        }
        return implode('/', $classes);
    }
}
