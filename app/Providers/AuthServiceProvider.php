<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Exception;
use App\Models\Store;
use App\Policies\ExceptionPolicy;
use App\Policies\StorePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Exception::class => ExceptionPolicy::class,
        Store::class => StorePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
