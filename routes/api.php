<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * @var Router $router
 */

$router->group(['prefix' => 'users'], function (Router $router) {
    $router->post('')->name(UsersController::ROUTE_NAME_REGISTER)->uses('UsersController@register');
});

$router->post('auth')->name(AuthController::ROUTE_NAME_AUTHENTICATE)->uses('AuthController@authenticate');

$router->group(['middleware' => 'auth'], function (Router $router) {

    $router->get('/me')->name(AuthController::ROUTE_NAME_AUTHENTICATED)->uses('AuthController@authenticated');
    $router->post('/logout')->name(AuthController::ROUTE_NAME_LOGOUT)->uses('AuthController@logout');

    $router->group(['prefix' => 'users'], function (Router $router) {
        $router->patch('')->name(UsersController::ROUTE_NAME_UPDATE)->uses('UsersController@update');
    });

});
