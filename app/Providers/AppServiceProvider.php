<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\User;
use App\Policies\ItemPolicy;
use App\Policies\ItemRequestPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Mendaftarkan Policy untuk otorisasi barang & permintaan
        Gate::policy(Item::class, ItemPolicy::class);
        Gate::policy(ItemRequest::class, ItemRequestPolicy::class);

        // Definisi Gate untuk hak akses role Admin
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        // Definisi Gate untuk hak akses role User biasa
        Gate::define('user', function (User $user) {
            return $user->isUser();
        });
    }
}
