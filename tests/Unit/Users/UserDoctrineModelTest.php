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

    /**
     * @return void
     */
    public function testGetAuthIdentifier(): void
    {
        $user = $this->getUserDoctrineModel()->setId($this->getFaker()->numberBetween());

        $this->assertEquals($user->getId(), $user->getAuthIdentifier());
    }

    /**
     * @return void
     */
    public function testGetAuthIdentifierName(): void
    {
        $this->assertEquals('id', $this->getUserDoctrineModel()->getAuthIdentifierName());
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
