<?php

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

Route::get('/',['as'=>'admin.index', 'uses'=>'HomeController@index']);

Route::group(['middleware' => 'auth'], function () {
    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes
   
    Route::group(['prefix'=>'shops'], function(){
        Route::group(['prefix'=>'add-shop'], function(){
            Route::get('/','Admin\ShopController@addShop');
            Route::post('/','Admin\ShopController@postAddShop');
        });
        Route::get('/','Admin\ShopController@index');
        Route::get('/delete/{period_id}','Admin\ShopController@delete');
        Route::get('/detail/{period_id}','Admin\ShopController@detail');
        Route::get('/export/{period_id}','Admin\ShopController@export');
    });
    Route::group(['prefix'=>'kelola-pemberian-kredit'], function(){
        Route::get('/','Admin\PemberianKreditController@index');
        Route::get('/add','Admin\PemberianKreditController@add');
        Route::post('/add','Admin\PemberianKreditController@postAdd');
        Route::get('/delete/{period_id}','Admin\PemberianKreditController@delete');
        Route::get('/detail/{period_id}','Admin\PemberianKreditController@detail');
        Route::get('/export/{period_id}','Admin\PemberianKreditController@export');
    });
    Route::group(['prefix'=>'export'], function(){
        Route::get('/','Admin\DataController@indexExport');
        Route::get('/{period_id}','Admin\DataController@export');
    });
});