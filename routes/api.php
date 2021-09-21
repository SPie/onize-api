<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitationsController;
use App\Http\Controllers\ProjectsController;
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
        $router->patch('password')->name(UsersController::ROUTE_NAME_UPDATE_PASSWORD)->uses('UsersController@updatePassword');
    });

    $router->group(['prefix' => 'projects'], function (Router $router) {
        $router->get('')->name(ProjectsController::ROUTE_NAME_USERS_PROJECTS)->uses('ProjectsController@usersProjects');
        $router->post('')->name(ProjectsController::ROUTE_NAME_CREATE)->uses('ProjectsController@create');

        $router->get('{project}/members')->name(ProjectsController::ROUTE_NAME_MEMBERS)->middleware('can:members,project')
            ->uses('ProjectsController@members');
        $router->delete('{project}/members/{user}')->name(ProjectsController::ROUTE_NAME_REMOVE_MEMBER)
            ->middleware('can:removeMember,project,user')
            ->uses('ProjectsController@removeMember');

        $router->get('{project}')->name(ProjectsController::ROUTE_NAME_SHOW)->middleware('can:show,project')->uses('ProjectsController@show');

        $router->post('{project}/invitations')->name(InvitationsController::ROUTE_NAME_INVITE)
            ->middleware('can:invite,project')
            ->uses('InvitationsController@invite');
        $router->post('invitations/{invitation}')->name(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION)
            ->middleware('can:accept,invitation')
            ->uses('InvitationsController@acceptInvitation');
        $router->delete('invitations/{invitation}')->name(InvitationsController::ROUTE_NAME_DECLINE_INVITATION)
            ->middleware('can:decline,invitation')
            ->uses('InvitationsController@declineInvitation');
    });
});
