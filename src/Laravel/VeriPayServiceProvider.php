<?php

namespace VeripayTT\SDK\Laravel;

use Illuminate\Support\ServiceProvider;
use VeripayTT\SDK\VeriPayClient;

class VeriPayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/veripaytt.php', 'veripaytt');

        $this->app->singleton(VeriPayClient::class, function ($app) {
            $config = config('veripaytt');
            $env = $config['environment'] ?? 'production';
            $baseUrl = $config['base_urls'][$env] ?? $config['base_urls']['production'];

            $config['base_url'] = $baseUrl;

            return new VeriPayClient(
                $config['api_key'],
                $config,
                $app->has('log') ? $app->get('log') : null
            );
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/veripaytt.php' => config_path('veripaytt.php'),
        ], 'veripaytt-config');
    }
}
