<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\URL;
use LaravelDoctrine\Migrations\Testing\DatabaseMigrations;
use Tests\Feature\FeatureTestCase;
use Tests\Helper\ApiHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;

/**
 * Class AuthApiCallsTest
 *
 * @package Tests\Feature\ApiCalls
 */
final class AuthApiCallsTest extends FeatureTestCase
{
    use ApiHelper;
    use DatabaseMigrations;
    use ModelHelper;
    use UsersHelper;

    //region Tests

    /**
     * @param bool $withAuthenticatedUser
     *
     * @return array
     */
    private function setUpAuthenticatedUserTest(bool $withAuthenticatedUser = true): array
    {
        $user = $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        return [$user];
    }

    /**
     * @return void
     */
    public function testAuthenticatedUser(): void
    {
        [$user] = $this->setUpAuthenticatedUserTest();

        $response = $this->doApiCall('GET', URL::route(AuthController::ROUTE_NAME_AUTHENTICATED));

        $response->assertStatus(200);
        $response->assertJsonFragment(['user' => $user->toArray()]);
    }

    /**
     * @return void
     */
    public function testAuthenticatedUserWithoutAuthenticatedUser(): void
    {
        $this->setUpAuthenticatedUserTest(false);

        $response = $this->doApiCall('GET', URL::route(AuthController::ROUTE_NAME_AUTHENTICATED));

        $response->assertStatus(401);
    }

    //endregion
}
