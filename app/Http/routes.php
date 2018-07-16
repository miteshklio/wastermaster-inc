<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * Home Page
 */

Route::get('/', 'App\HomeController@index');

/**
 * Auth
 */

// Login
Route::post('login', ['middleware' => 'csrf', 'uses' => 'Auth\AuthController@login']);

// Logout
Route::get('logout', 'Auth\AuthController@logout');

/**
 * Password Reset
 */

Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
Route::post('password/email', ['middleware' => 'csrf', 'uses' => 'Auth\PasswordController@sendResetLinkEmail']);
Route::post('password/reset', ['middleware' => 'csrf', 'uses' => 'Auth\PasswordController@reset']);

/**
 * Image Cropping
 */

Route::get('cropped/width/{width}/height/{height}/{img}/{position?}', 'ImageController@crop');


/**
 * Admin
 */

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'admin'], function()
{
    // Dashboard
    Route::get('dashboard', 'DashboardController@index');

    // Users
    Route::match(['get', 'post'], 'users', 'UserController@index');
    Route::get('user', 'UserController@newUser');
    Route::post('user', 'UserController@create');
    Route::get('user/{id}', 'UserController@get');
    Route::post('user/{id}', 'UserController@update');
    Route::get('user/{id}/delete', 'UserController@delete');

    // Haulers
    Route::match(['get', 'post'], 'haulers', 'HaulerController@index')->name('haulers::home');
    Route::get('hauler', 'HaulerController@newHauler')->name('haulers::new');
    Route::post('hauler', 'HaulerController@create')->name('haulers::create');
    Route::get('hauler/{id}', 'HaulerController@show')->name('haulers::show');
    Route::post('hauler/{id}', 'HaulerController@update')->name('haulers::update');
    Route::get('hauler/{id}/delete', 'HaulerController@delete')->name('haulers::delete');
    Route::get('hauler/{id}/archive', 'HaulerController@archive')->name('haulers::archive');
    Route::get('hauler/{id}/unarchive', 'HaulerController@unarchive')->name('haulers::unarchive');

    // Leads
    Route::match(['get', 'post'], 'leads', 'LeadsController@index')->name('leads::home');
    Route::get('lead', 'LeadsController@newLead')->name('leads::new');
    Route::post('lead', 'LeadsController@create')->name('leads::create');
    Route::get('lead/{id}', 'LeadsController@show')->name('leads::show');
    Route::post('lead/{id}', 'LeadsController@update')->name('leads::update');
    Route::get('lead/{id}/delete', 'LeadsController@delete')->name('leads::delete');
    Route::get('lead/{id}/archive', 'LeadsController@archive')->name('leads::archive');
    Route::get('lead/{id}/unarchive', 'LeadsController@unarchive')->name('leads::unarchive');
    Route::post('lead/{id}/send_bid_requests', 'LeadsController@sendBidRequest')->name('leads::sendBidRequest');
    Route::get('lead/{id}/convert', 'LeadsController@convertToClient')->name('leads::convert');
    Route::get('lead/{id}/rebid', 'LeadsController@rebid')->name('leads::rebid');

    // Clients
    Route::match(['get', 'post'], 'clients', 'ClientController@index')->name('clients::home');
    Route::get('client', 'ClientController@newClient')->name('clients::new');
    Route::post('client', 'ClientController@create')->name('clients::create');
    Route::get('client/{id}', 'ClientController@show')->name('clients::show');
    Route::post('client/{id}', 'ClientController@update')->name('clients::update');
    Route::get('client/{id}/delete', 'ClientController@delete')->name('clients::delete');
    Route::get('client/{id}/archive', 'ClientController@archive')->name('clients::archive');
    Route::get('client/{id}/unarchive', 'ClientController@unarchive')->name('clients::unarchive');
    Route::get('client/{id}/rebid', 'ClientController@rebid')->name('clients::rebid');

    // Bids
    Route::match(['get', 'post'], 'bids', 'BidController@index')->name('bids::home');
    Route::get('bid', 'BidController@newClient')->name('bids::new');
    Route::post('bid', 'BidController@create')->name('bids::create');
    Route::get('bid/{id}', 'BidController@show')->name('bids::show');
    Route::post('bid/{id}', 'BidController@update')->name('bids::update');
    Route::get('bid/{id}/delete', 'BidController@delete')->name('bids::delete');
    Route::post('bid/{id}/accept', 'BidController@accept')->name('bids::accept');
    Route::get('bid/{id}/rescind', 'BidController@rescind')->name('bids::rescind');
    Route::get('bid/{id}/post_match_request', 'BidController@postMatchRequest')->name('bids::postMatchRequest');
    Route::get('bid/{id}/get_accept_modal', 'BidController@acceptModal')->name('bids::getAcceptModal');

    // Service Areas
    Route::match(['get', 'post'], 'areas', 'AreasController@index')->name('areas::home');
    Route::get('area', 'AreasController@newArea')->name('areas::new');
    Route::post('area', 'AreasController@create')->name('areas::create');
    Route::get('area/{id}', 'AreasController@show')->name('areas::show');
    Route::post('area/{id}', 'AreasController@update')->name('areas::update');
    Route::get('area/{id}/delete', 'AreasController@delete')->name('areas::delete');
});

/**
 * External Forms
 */
Route::get('bid/{id}', 'BidController@showForm')->name('bids::externalForm');
Route::post('bid/{code}', 'BidController@submitBid')->name('bids::submitBid');
Route::get('bid/{code}/thanks', 'BidController@thanks')->name('bids::thanks');

/**
 * AJAX
 */
Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax'], function()
{
    Route::get('cities/autocomplete', 'CityController@autocomplete');
});
