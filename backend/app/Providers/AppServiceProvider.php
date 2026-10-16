<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Rproject;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            try {
                $donutRprojects = Rproject::where('status', 1)->orderBy('id', 'asc')->get();
                $view->with('donutRprojects', $donutRprojects);
            } catch (\Throwable $e) {
                $view->with('donutRprojects', collect());
            }
        });
    }
}
