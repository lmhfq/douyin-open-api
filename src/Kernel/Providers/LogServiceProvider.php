<?php


namespace Lmh\DouyinOpenApi\Kernel\Providers;

use Lmh\DouyinOpenApi\Kernel\LogManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class LoggingServiceProvider.
 *
 * @author overtrue <i@overtrue.me>
 */
class LogServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['logger'] = function ($app) {
            $config = $this->formatLogConfig($app);
            if (!empty($config)) {

                $app->rebind('config', $app['config']->merge($config));
            }
            return new LogManager($app);
        };
    }

    public function formatLogConfig($app): array
    {
        if (empty($app['config']->get('log'))) {
            return [
                'log' => [
                    'default' => 'errorlog',
                    'channels' => [
                        'errorlog' => [
                            'driver' => 'errorlog',
                            'level' => 'debug',
                        ],
                    ],
                ],
            ];
        }
        return [
            'log' => [
                'default' => 'single',
                'channels' => [
                    'single' => [
                        'driver' => 'single',
                        'path' => $app['config']->get('log.file') ?: \sys_get_temp_dir() . '/logs/douyin.log',
                        'level' => $app['config']->get('log.level', 'debug'),
                    ],
                ],
            ],
        ];
    }
}
