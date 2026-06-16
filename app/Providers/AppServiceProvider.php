<?php

namespace App\Providers;

use App\Http\Services\Abstract\TransaksiService;
use App\Http\Services\Implementation\TransaksiServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        TransaksiService::class => TransaksiServiceImpl::class,
    ];

    public function provides()
    {
        return [
            TransaksiService::class,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
