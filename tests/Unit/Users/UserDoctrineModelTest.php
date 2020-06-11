<?php

namespace Tests\Unit\Users;

use App\Users\UserDoctrineModel;
use Tests\TestCase;

/**
 * Class UserDoctrineModelTest
 *
 * @package Tests\Unit\Users
 */
final class UserDoctrineModelTest extends TestCase
{
    //region Tests

    /**
     * @return void
     */
    public function testToarray(): void
    {
        $uuid = $this->getFaker()->uuid;
        $email = $this->getFaker()->safeEmail;

        $this->assertEquals(
            [
                'uuid' => $uuid,
                'email' => $email,
            ],
            $this->getUserDoctrineModel($uuid, $email)->toArray()
        );
    }

    //endregion

    /**
     * @param string|null $uuid
     * @param string|null $email
     * @param string|null $password
     *
     * @return UserDoctrineModel
     */
    private function getUserDoctrineModel(string $uuid = null, string $email = null, string $password = null): UserDoctrineModel
    {
        return new UserDoctrineModel(
            $uuid ?: $this->getFaker()->uuid,
            $email ?: $this->getFaker()->safeEmail,
            $password ?: $this->getFaker()->password
        );
    }
}
