<?php

namespace Lmh\DouyinOpenApi\Service\GoodLife\Auth;

use Lmh\DouyinOpenApi\Kernel\AbstractAccessToken;

class AccessToken extends AbstractAccessToken
{
    /**
     * 生成 client_token 测试使用
     * @param bool $refresh
     * @return string[]
     */
    public function getToken(bool $refresh = false): array
    {
        return [
            'access_token' => 'clt.6810ca0f41a54c4d79fa3a91c7841921RoOOkCR2XkOyOKd14KNHgNcNj2Yd'
        ];
    }

    protected function getCredentials(): array
    {
        return [
            'grant_type' => 'client_credential',
            'client_key' => $this->app['config']['app_id'],
            'client_secret' => $this->app['config']['app_secret'],
        ];
    }
}