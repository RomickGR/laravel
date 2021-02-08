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


Route::get('/', 'App\Http\Controllers\PostController@index')->name('home');
Route::get('/article', 'App\Http\Controllers\PostController@show')->name('posts.single');

Route::group(['prefix'=>'admin', 'namesapace'=>'App\Http\Controllers\Admin', 'middleware'=>'admin'], function() {
    Route::get('/','App\Http\Controllers\Admin\MainController@index')->name('admin.index');
    Route::resource('/categories', '\App\Http\Controllers\Admin\CategoryController');
    Route::resource('/tags', '\App\Http\Controllers\Admin\TagController');
    Route::resource('/posts', '\App\Http\Controllers\Admin\PostController');
    }
);

Route::group(['middleware'=>'guest'],function(){
    Route::get('/register','\App\Http\Controllers\UserController@create')->name('register.create');
    Route::post('/register','\App\Http\Controllers\UserController@store')->name('register.store');
    Route::get('/login','\App\Http\Controllers\UserController@loginForm')->name('login.create');
    Route::post('/login','\App\Http\Controllers\UserController@login')->name('login');
});


Route::get('/logout','\App\Http\Controllers\UserController@logout')->name('logout')->middleware('auth');


