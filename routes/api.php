<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Api\StoreController as ApiStoreController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Api\CouponController as ApiCouponController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Api\BlogController as ApiBlogController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Api\PageController as ApiPageController;
use App\Http\Controllers\Admin\CountryController as AdminCountryController;
use App\Http\Controllers\Api\CountryController as ApiCountryController;
use App\Http\Controllers\Admin\SeoController as AdminSeoController;
use App\Http\Controllers\Api\SeoController as ApiSeoController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\HomeController as ApiHomeController;
use App\Http\Controllers\Api\SearchController as ApiSearchController;
use App\Http\Controllers\Api\EventController as ApiEventController;

Route::post('/auth/login', [AuthController::class, 'login']);

// Public Endpoints
Route::get('/categories', [ApiCategoryController::class, 'index']);
Route::get('/categories/{id}', [ApiCategoryController::class, 'show']);

Route::get('/stores', [ApiStoreController::class, 'index']);
Route::get('/stores/{id}', [ApiStoreController::class, 'show']);

Route::get('/coupons', [ApiCouponController::class, 'index']);
Route::get('/coupons/{id}', [ApiCouponController::class, 'show']);
Route::post('/coupons/{id}/click', [ApiCouponController::class, 'click']);

Route::get('/products', [ApiProductController::class, 'index']);
Route::get('/products/{id}', [ApiProductController::class, 'show']);
Route::post('/products/{id}/click', [ApiProductController::class, 'click']);

Route::get('/blog', [ApiBlogController::class, 'index']);
Route::get('/blog/{id}', [ApiBlogController::class, 'show']);

Route::get('/pages', [ApiPageController::class, 'index']);
Route::get('/pages/{slug}', [ApiPageController::class, 'show']);

Route::get('/countries', [ApiCountryController::class, 'index']);
Route::get('/countries/{code}', [ApiCountryController::class, 'show']);

Route::get('/seo/{pageKey}', [ApiSeoController::class, 'show']);

Route::get('/home', [ApiHomeController::class, 'index']);
Route::get('/search', [ApiSearchController::class, 'index']);

// Event Endpoints
Route::post('/events/page-view', [ApiEventController::class, 'pageView']);
Route::post('/events/store-click', [ApiEventController::class, 'storeClick']);
Route::post('/events/coupon-click', [ApiCouponController::class, 'click']);
Route::post('/events/product-click', [ApiProductController::class, 'click']);
Route::post('/events/newsletter-subscribe', [ApiEventController::class, 'newsletterSubscribe']);
Route::post('/events/search', [ApiEventController::class, 'search']);

// Admin Endpoints
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::apiResource('categories', AdminCategoryController::class);
    Route::apiResource('stores', AdminStoreController::class);

    // IMPORTANT: bulk route must come BEFORE apiResource to avoid being captured as a coupon ID
    Route::patch('/coupons/bulk-update-expiry', [AdminCouponController::class, 'bulkUpdateExpiry']);
    Route::apiResource('coupons', AdminCouponController::class);

    Route::apiResource('products', AdminProductController::class);

    Route::apiResource('blog', AdminBlogController::class);

    Route::apiResource('pages', AdminPageController::class);

    Route::patch('/countries/bulk-update', [AdminCountryController::class, 'bulkUpdate']);
    Route::get('/countries', [AdminCountryController::class, 'index']);
    Route::put('/countries/{code}', [AdminCountryController::class, 'update']);

    Route::get('/seo', [AdminSeoController::class, 'index']);
    Route::put('/seo', [AdminSeoController::class, 'update']);

    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    Route::prefix('analytics')->group(function () {
        Route::get('/overview', [AdminAnalyticsController::class, 'overview']);
        Route::get('/stores', [AdminAnalyticsController::class, 'stores']);
        Route::get('/coupons', [AdminAnalyticsController::class, 'coupons']);
        Route::get('/products', [AdminAnalyticsController::class, 'products']);
    });

    Route::post('/upload', [\App\Http\Controllers\Admin\FileUploadController::class, 'store']);
});
