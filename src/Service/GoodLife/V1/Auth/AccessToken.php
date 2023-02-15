<?php

namespace Lmh\DouyinOpenApi\Service\GoodLife\V1\Auth;

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
            'access_token' => 'clt.15257f34af3ef108dac4b87569b9fa4bJXxRdi09nH86NtWHUsYFO3NlDoH1'
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