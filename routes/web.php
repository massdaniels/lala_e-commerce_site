<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use App\Http\Controllers\AdminController;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', AuthAdmin::class])->group(function(){
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand/new', [AdminController::class, 'new_brand'])->name('admin.brand.new');
    Route::post('/admin/brand/create', [AdminController::class, 'create_brand'])->name('admin.brand.create');
    Route::get('admin/brand/edit/{id}', [AdminController::class, 'edit_brand'])->name('admin.brand.edit');
    Route::put('admin/brand/update', [AdminController::class, 'update_brand'])->name('admin.brand.update');
    Route::delete('admin/brand/delete/{id}',[AdminController::class, 'delete_brand'])->name('admin.brand.delete');
   
    Route::get('admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('admin/category/new', [AdminController::class, 'new_category'])->name('admin.category.new');
    Route::post('admin/category/create', [AdminController::class, 'create_category'])->name('admin.category.create');
    Route::delete('admin/category/delete/{id}', [AdminController::class, 'delete_category'])->name('admin.category.delete');
    Route::get('admin/category/edit/{id}', [AdminController::class, 'edit_cateogry'])->name('admin.category.edit');
    Route::put('admin/category/update', [AdminController::class, 'update_category'])->name('admin.category.update');

    Route::get('admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('admin/product/new', [AdminController::class, 'new_product'])->name('admin.product.new');
    Route::post('admin/product/create', [AdminController::class, 'create_product'])->name('admin.product.create');
    Route::get('admin/product/edit/{id}', [AdminController::class, 'edit_product'])->name('admin.product.edit');
    Route::put('admin/product/update', [AdminController::class, 'update_product'])->name('admin.product.update');
});
