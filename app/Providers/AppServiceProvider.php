<?php

namespace App\Providers;

use ConsoleTVs\Charts\Registrar as Charts;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Charts $charts)
    {
        \Illuminate\Pagination\Paginator::useBootstrap();
        $charts->register([
            \App\Charts\AgcDonor::class,
            \App\Charts\AgcAmount::class,
            \App\Charts\BoardParticipants::class,
            \App\Charts\BoardPeoplenights::class,
            \App\Charts\BoardRevenue::class,
            \App\Charts\DonationDescription::class,
        ]);
    }
}
