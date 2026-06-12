<?php

namespace App\Providers;

use App\Models\Depense;
use App\Models\Recu;
use App\Policies\DepensePolicy;
use App\Policies\RecuPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::policy(Recu::class, RecuPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Depense::class, DepensePolicy::class);

        Paginator::useBootstrapFive();
    }
}
