<?php

use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\SecurityController;
use App\Http\Controllers\Admin\ContentGateController as AdminContentGateController;
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
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MarketplaceTermsController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LegalPageController;
use App\Http\Controllers\Public\MatchController;
use App\Http\Controllers\Public\PickemController;
use App\Http\Controllers\Public\ShopController;
use App\Http\Controllers\Public\TeamController;
use App\Http\Controllers\SkinListingController;
use App\Http\Controllers\SteamOpenIdController;
use App\Http\Controllers\SteamProfileController;
use App\Http\Controllers\TradeRequestController;
use App\Http\Controllers\UserSearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/contact', [ContactController::class, 'create'])
    ->name('contact.create');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');

Route::get('/matches', [MatchController::class, 'index'])
    ->name('matches.index');

Route::get('/matches/{match}', [MatchController::class, 'show'])
    ->name('matches.show');

Route::get('/pickem', [PickemController::class, 'index'])
    ->name('pickem.index');

Route::get('/pickem/{event}', [PickemController::class, 'show'])
    ->name('pickem.show');

Route::get('/shop', [ShopController::class, 'index'])
    ->name('shop.index');

Route::get('/shop/{product}', [ShopController::class, 'show'])
    ->name('shop.show');

Route::get('/teams', [TeamController::class, 'index'])
    ->name('teams.index');

Route::get('/teams/{team}', [TeamController::class, 'show'])
    ->name('teams.show');

/*
|--------------------------------------------------------------------------
| Legal routes
|--------------------------------------------------------------------------
*/

Route::get('/affiliate-disclosures', [LegalPageController::class, 'affiliateDisclosures'])
    ->name('legal.affiliate');

Route::get('/data-usage-collection-policy', [LegalPageController::class, 'dataUsageCollectionPolicy'])
    ->name('legal.data');

Route::get('/disclaimer', [LegalPageController::class, 'disclaimer'])
    ->name('legal.disclaimer');

Route::get('/privacy-policy', [LegalPageController::class, 'privacyPolicy'])
    ->name('legal.privacy');

Route::get('/terms-of-service', [LegalPageController::class, 'termsOfService'])
    ->name('legal.terms');

/*
|--------------------------------------------------------------------------
| Public marketplace routes
|--------------------------------------------------------------------------
*/

Route::get('/marketplace', [MarketplaceController::class, 'index'])
    ->name('marketplace.index');

Route::get('/marketplace/listings/{listing}', [MarketplaceController::class, 'show'])
    ->name('marketplace.listings.show');

/*
|--------------------------------------------------------------------------
| Guest auth routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'apple', 'orcid'])
        ->name('social.callback');

    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'apple', 'orcid'])
        ->name('social.redirect');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
        ->name('password.email');

    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'store'])
        ->name('register.store');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'store'])
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Email verification routes
|--------------------------------------------------------------------------
*/

Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::middleware('auth')->group(function () {
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('/email/verify-code', [EmailVerificationController::class, 'verifyCode'])
        ->name('verification.code.verify');
});

/*
|--------------------------------------------------------------------------
| Account routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('account')
    ->name('account.')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'show'])
            ->name('show');

        Route::get('/edit', [ProfileController::class, 'edit'])
            ->name('edit');

        Route::put('/edit', [ProfileController::class, 'update'])
            ->name('update');

        Route::get('/security', [SecurityController::class, 'edit'])
            ->name('security');

        Route::put('/security/password', [SecurityController::class, 'updatePassword'])
            ->name('password.update');
    });

/*
|--------------------------------------------------------------------------
| Marketplace setup routes
|--------------------------------------------------------------------------
| These require login, but not full marketplace readiness.
*/

Route::middleware('auth')->group(function () {
    Route::get('/marketplace/terms', [MarketplaceTermsController::class, 'show'])
        ->name('marketplace.terms');

    Route::post('/marketplace/terms', [MarketplaceTermsController::class, 'accept'])
        ->name('marketplace.terms.accept');

    Route::get('/profile/steam', [SteamProfileController::class, 'show'])
        ->name('profile.steam');

    Route::get('/users/search', [UserSearchController::class, 'index'])
        ->name('users.search');

    Route::get('/profile/steam/callback', [SteamOpenIdController::class, 'callback'])
        ->name('profile.steam.callback');

    Route::get('/profile/steam/link', [SteamOpenIdController::class, 'redirect'])
        ->name('profile.steam.link');

    Route::delete('/profile/steam/unlink', [SteamProfileController::class, 'unlinkSteamAccount'])
        ->name('profile.steam.unlink');

    Route::post('/profile/steam/inventory/sync', [SteamProfileController::class, 'syncInventory'])
        ->name('profile.steam.inventory.sync');

    Route::post('/profile/steam/refresh', [SteamProfileController::class, 'refreshSteamProfile'])
        ->name('profile.steam.refresh');

    Route::post('/profile/steam/trade-url', [SteamProfileController::class, 'updateTradeUrl'])
        ->name('profile.steam.trade-url.update');
});

/*
|--------------------------------------------------------------------------
| Marketplace user routes
|--------------------------------------------------------------------------
| These require login, verified email, accepted terms, Steam link,
| trade URL, public profile, and public inventory.
*/

Route::middleware(['auth', 'verified', 'marketplace.ready'])->group(function () {
    Route::get('/marketplace/my-listings', [SkinListingController::class, 'index'])
        ->name('marketplace.listings.index');

    Route::get('/marketplace/sell', [SkinListingController::class, 'create'])
        ->name('marketplace.listings.create');

    Route::post('/marketplace/listings', [SkinListingController::class, 'store'])
        ->name('marketplace.listings.store');

    Route::post('/marketplace/listings/{listing}/cancel', [SkinListingController::class, 'cancel'])
        ->name('marketplace.listings.cancel');

    Route::post('/marketplace/listings/{listing}/trade-request', [TradeRequestController::class, 'store'])
        ->name('marketplace.trade-requests.store');

    Route::get('/marketplace/trade-requests', [TradeRequestController::class, 'index'])
        ->name('marketplace.trade-requests.index');

    Route::post('/marketplace/trade-requests/{tradeRequest}/accept', [TradeRequestController::class, 'accept'])
        ->name('marketplace.trade-requests.accept');

    Route::post('/marketplace/trade-requests/{tradeRequest}/cancel', [TradeRequestController::class, 'cancel'])
        ->name('marketplace.trade-requests.cancel');

    Route::post('/marketplace/trade-requests/{tradeRequest}/complete', [TradeRequestController::class, 'complete'])
        ->name('marketplace.trade-requests.complete');

    Route::post('/marketplace/trade-requests/{tradeRequest}/decline', [TradeRequestController::class, 'decline'])
        ->name('marketplace.trade-requests.decline');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('events', AdminEventController::class);
        Route::resource('matches', AdminMatchController::class);
        Route::resource('pickem', AdminPickemRecommendationController::class);
        Route::resource('players', AdminPlayerController::class);
        Route::resource('predictions', AdminPredictionController::class);
        Route::resource('products', AdminProductController::class);
        Route::resource('teams', AdminTeamController::class);

        Route::get('content-gates', [AdminContentGateController::class, 'index'])
            ->name('content-gates.index');

        Route::put('content-gates/{contentGate}', [AdminContentGateController::class, 'update'])
            ->name('content-gates.update');

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

        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('orders.update-status');

        Route::resource('orders', AdminOrderController::class)
            ->only(['index', 'show']);

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
    });