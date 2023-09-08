<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', 'HomeController@index')->name('home.index');
    Route::get('/', 'HomeController@index')->name('home.index');
    Route::get('logout', 'HomeController@logout')->name('home.logout');
// Permissions management
    Route::get('user-management/permissions', 'UserManagementController@index')->name('user-management.permissions.index');
    Route::get('user-management/permissions/upsert/{permission_id?}', 'UserManagementController@upsert')->name('user-management.permissions.upsert');
    Route::get('user-management/permissions/delete/{permission_id?}', 'UserManagementController@deletePermission')->name('user-management.permissions.delete-permission');
    Route::post('user-management/permissions/postdata/{permission_id?}', 'UserManagementController@postData')->name('user-management.permissions.postdata');
// Shop mangement
    Route::get('shop-management', 'ShopManagementController@index')->name('shop-management.index');
    Route::get('shop-management/upsert/{shop_id?}', 'ShopManagementController@upsert')->name('shop-management.upsert');
    Route::post('shop-management/postdata/{shop_id?}', 'ShopManagementController@postdata')->name('shop-management.postdata');
    Route::get('shop-management/delete/{shop_id}', 'ShopManagementController@deleteShop')->name('shop-management.delete-shop');
// Product management
    Route::get('product-management', 'ProductManagementController@index')->name('product-management.index');
    Route::get('product-management/upsert/{product_id?}', 'ProductManagementController@upsert')->name('product-management.upsert');
    Route::post('product-management/postdata/{product_id?}', 'ProductManagementController@postData')->name('product-management.postdata');
    Route::get('product-management/delete/{product_id}', 'ProductManagementController@deleteProduct')->name('product-management.delete-product');
    Route::get('product-management/delete-image/{product_name}', 'ProductManagementController@deleteProductImage')->name('product-management.delete-product-image');
// Stores
    Route::get('shops', 'ShoppingManagementController@index')->name('shops.index');
    Route::get('shops/{shop_id}', 'ShoppingManagementController@shopProducts')->name('shops.products');
    Route::get('product/{product_id}/store/{store_id?}', 'ShoppingManagementController@viewProduct')->name('view-product');
// Shopping Cart
    Route::get('shopping-carts', 'ShoppingCartController@index')->name('shopping-carts.index');
    Route::get('shopping-carts/upsert/{cart_id?}', 'ShoppingCartController@upsert')->name('shopping-carts.upsert');
    Route::get('shopping-carts/copy/{cart_id}', 'ShoppingCartController@copyCart')->name('shopping-carts.copy');
    Route::get('shopping-carts/delete/{cart_id}', 'ShoppingCartController@deleteCart')->name('shopping-carts.delete-cart');
    Route::post('shopping-carts/postdata/{cart_id?}', 'ShoppingCartController@postData')->name('shopping-carts.postdata');
    Route::get('add-to-cart', 'ShoppingCartController@addToCart')->name('add-to-cart');
    Route::get('shopping-carts/resolve-action', 'ShoppingCartController@resolveAction')->name('shopping-carts.resolve');
// Shopping Cart Statistics
    Route::get('shopping-carts/statistics', 'ShoppingCartController@statistics')->name('shopping-carts.statistics');
});

Route::get('api/products', 'apiController@getProducts')->name('api-products');
Route::get('api/shops', 'apiController@getShops')->name('api-shops');





