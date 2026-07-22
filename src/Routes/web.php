<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:super-admin|developer|admin|employee', 'license'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/kds/displays', \Dev3bdulrahman\Kds\Http\Controllers\Web\Admin\Displays\Index::class)->name('admin.kds.displays');
        Route::get('/kds/orders', \Dev3bdulrahman\Kds\Http\Controllers\Web\Admin\Orders\Index::class)->name('admin.kds.orders');
        Route::get('/kds/screen/{display}', \Dev3bdulrahman\Kds\Http\Controllers\Web\Admin\Screen\View::class)->name('admin.kds.screen');
    });
