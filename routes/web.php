<?php

use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/hosting-plans', [PublicPageController::class, 'hosting'])->name('hosting');
Route::get('/wordpress-hosting', [PublicPageController::class, 'wordpress'])->name('wordpress');
Route::get('/kurumsal-hosting', [PublicPageController::class, 'corporate'])->name('corporate');
Route::get('/vps-vds', [PublicPageController::class, 'vps'])->name('vps');
Route::get('/dedicated-servers', [PublicPageController::class, 'dedicated'])->name('dedicated');
Route::get('/radio-hosting', [PublicPageController::class, 'radio'])->name('radio');
Route::get('/tv-hosting', [PublicPageController::class, 'tv'])->name('tv');
Route::get('/about-us', [PublicPageController::class, 'about'])->name('about');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
Route::get('/faq', [PublicPageController::class, 'faq'])->name('faq');
Route::get('/blog', [PublicPageController::class, 'blog'])->name('blog');
Route::get('/blog/{post:slug}', [PublicPageController::class, 'post'])->name('blog.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);
    Route::resource('products', AdminProductController::class);
    Route::resource('blog-posts', AdminBlogPostController::class);
    Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');
});
