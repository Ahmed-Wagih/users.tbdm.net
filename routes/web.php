<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});


$router->group(['prefix' => 'v1', 'namespace' => 'Api\v1'], function() use($router)
{
    // $app->post('users/loasdgasdiasdnasd', ['middleware' => 'auth', 'uses' => 'UsersController@authenticate']);
    $router->get('users', 'UserController@index');
    $router->get('users/check-phone/{phone}', 'UserController@checkPhoneNumber');
    $router->get('users/check-otp/{otp}/user/{userId}', 'UserController@checkOtp');
    $router->get('users/vendors', 'UserController@getVendors');
    $router->get('users/doctors', 'UserController@getDoctors');
    $router->get('users/{id}', 'UserController@show');
    $router->post('users', 'UserController@store');
    $router->put('users/{id}', 'UserController@update');
    $router->post('users/login', 'UserController@login');
    $router->post('users/vendor/register', 'UserController@vendorRegister');
    $router->patch('users/{id}', 'UserController@update');
    $router->delete('users/{id}', 'UserController@destroy');
});
