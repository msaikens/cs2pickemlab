<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\EventStageController as AdminEventStageController;
use App\Http\Controllers\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PickemRecommendationController as AdminPickemRecommendationController;
use App\Http\Controllers\Admin\PlayerController as AdminPlayerController;
use App\Http\Controllers\Admin\PredictionController as AdminPredictionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductOptionController as AdminProductOptionController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\MatchController;
use App\Http\Controllers\Public\PickemController;
use App\Http\Controllers\Public\ShopController;
use App\Http\Controllers\Public\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');

Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');

Route::get('/pickem', [PickemController::class, 'index'])->name('pickem.index');
Route::get('/pickem/{event}', [PickemController::class, 'show'])->name('pickem.show');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [ShopController::class, 'show'])->name('shop.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('teams', AdminTeamController::class);
    Route::resource('players', AdminPlayerController::class);

    Route::get('events/{event}/stages', [AdminEventStageController::class, 'index'])
        ->name('events.stages.index');

    Route::get('events/{event}/stages/create', [AdminEventStageController::class, 'create'])
        ->name('events.stages.create');

    Route::post('events/{event}/stages', [AdminEventStageController::class, 'store'])
        ->name('events.stages.store');

    Route::get('events/{event}/stages/{stage}/edit', [AdminEventStageController::class, 'edit'])
        ->name('events.stages.edit');

    Route::put('events/{event}/stages/{stage}', [AdminEventStageController::class, 'update'])
        ->name('events.stages.update');

    Route::delete('events/{event}/stages/{stage}', [AdminEventStageController::class, 'destroy'])
        ->name('events.stages.destroy');

    Route::resource('events', AdminEventController::class);
    Route::resource('matches', AdminMatchController::class);
    Route::resource('predictions', AdminPredictionController::class);
    Route::resource('pickem', AdminPickemRecommendationController::class);

    Route::get('products/{product}/variants', [AdminProductVariantController::class, 'index'])
        ->name('products.variants.index');

    Route::get('products/{product}/variants/create', [AdminProductVariantController::class, 'create'])
        ->name('products.variants.create');

    Route::post('products/{product}/variants', [AdminProductVariantController::class, 'store'])
        ->name('products.variants.store');

    Route::get('products/{product}/variants/{variant}/edit', [AdminProductVariantController::class, 'edit'])
        ->name('products.variants.edit');

    Route::put('products/{product}/variants/{variant}', [AdminProductVariantController::class, 'update'])
        ->name('products.variants.update');

    Route::delete('products/{product}/variants/{variant}', [AdminProductVariantController::class, 'destroy'])
        ->name('products.variants.destroy');

    Route::get('products/{product}/options', [AdminProductOptionController::class, 'index'])
        ->name('products.options.index');

    Route::get('products/{product}/options/create', [AdminProductOptionController::class, 'create'])
        ->name('products.options.create');

    Route::post('products/{product}/options', [AdminProductOptionController::class, 'store'])
        ->name('products.options.store');

    Route::get('products/{product}/options/{option}/edit', [AdminProductOptionController::class, 'edit'])
        ->name('products.options.edit');

    Route::put('products/{product}/options/{option}', [AdminProductOptionController::class, 'update'])
        ->name('products.options.update');

    Route::delete('products/{product}/options/{option}', [AdminProductOptionController::class, 'destroy'])
        ->name('products.options.destroy');

    Route::resource('products', AdminProductController::class);

    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
        ->name('orders.update-status');

    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
});
