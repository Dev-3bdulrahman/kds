<?php

namespace Dev3bdulrahman\Kds;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Dev3bdulrahman\Kds\Models\KdsDisplay;
use Dev3bdulrahman\Kds\Models\KdsOrder;
use Dev3bdulrahman\Kds\Policies\KdsDisplayPolicy;
use Dev3bdulrahman\Kds\Policies\KdsOrderPolicy;

class KdsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        $this->loadViewsFrom(__DIR__ . '/Views', 'kds');

        $this->loadTranslationsFrom(__DIR__ . '/Translations', 'kds');

        Gate::policy(KdsDisplay::class, KdsDisplayPolicy::class);
        Gate::policy(KdsOrder::class, KdsOrderPolicy::class);

        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('kds-display-index', \Dev3bdulrahman\Kds\Http\Controllers\Web\Admin\Displays\Index::class);
            \Livewire\Livewire::component('kds-orders-index', \Dev3bdulrahman\Kds\Http\Controllers\Web\Admin\Orders\Index::class);
            \Livewire\Livewire::component('kds-screen-view', \Dev3bdulrahman\Kds\Http\Controllers\Web\Admin\Screen\View::class);
        }
    }
}
