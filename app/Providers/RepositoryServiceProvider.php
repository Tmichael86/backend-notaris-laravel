<?php

namespace App\Providers;

use App\Http\Repositories\Abstract\TransaksiRepository;
use App\Http\Repositories\Implementation\TransaksiRepositoryImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        TransaksiRepository::class => TransaksiRepositoryImpl::class,
    ];

    public function provides()
    {
        return [
            TransaksiRepository::class,
        ];
    }

    public function register() {}

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
