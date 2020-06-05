<?php

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