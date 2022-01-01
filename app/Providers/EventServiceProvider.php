<?php

namespace App\Providers;

use App\Events\DynDNSUpdated;
use App\Events\UpdateDnyDNSIPError;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        DynDNSUpdated::class => [],
        UpdateDnyDNSIPError::class => []
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
