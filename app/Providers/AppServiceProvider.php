<?php

namespace App\Providers;

use App\Models\Contact;
use ConsoleTVs\Charts\Registrar as Charts;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

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
            \App\Charts\EventParticipants::class,
            \App\Charts\EventPeoplenights::class,
            \App\Charts\EventRevenue::class,
            \App\Charts\DonationDescription::class,
        ]);
        Cashier::useCustomerModel(Contact::class);
    }
}
