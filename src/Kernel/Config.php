<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Lmh\DouyinOpenApi\Kernel;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class Config.
 *
 * @author overtrue <i@overtrue.me>
 */
class Config extends Collection
{
    /**
     * Get a data by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }


    public function get($key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }
}
