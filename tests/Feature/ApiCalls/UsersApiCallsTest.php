<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\URL;
use LaravelDoctrine\Migrations\Testing\DatabaseMigrations;
use Tests\Feature\FeatureTestCase;
use Tests\Helper\ApiHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;

/**
 * Class UsersApiCallsTest
 *
 * @package Tests\Feature\ApiCalls
 */
final class UsersApiCallsTest extends FeatureTestCase
{
    use ApiHelper;
    use DatabaseMigrations;
    use ModelHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;

        $response = $this->doApiCall(
            'POST',
            URL::route(UsersController::ROUTE_NAME_REGISTER),
            [
                'email'    => $email,
                'password' => $password,
            ]
        );

        $response->assertCreated();
    }

    //endregion
}
