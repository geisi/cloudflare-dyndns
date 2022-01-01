<?php

namespace App\Providers;

use App\Contracts\DiscoversIpAddress;
use App\Services\CloudflareDynDNS;
use App\Services\OpenDNSPublicIPResolver;
use Illuminate\Support\ServiceProvider;

class CloudflareProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->app->singleton(DiscoversIpAddress::class, OpenDNSPublicIPResolver::class);
        $this->app->bind(CloudflareDynDNS::class, function ($app) {
            return new CloudflareDynDNS(config('services.cloudflare.key'),
                config('dyndns.subdomain'),
                $app->make(DiscoversIpAddress::class),
                config('dyndns.zoneId'),
                config('dyndns.recordId')
            );
        });
    }
}
