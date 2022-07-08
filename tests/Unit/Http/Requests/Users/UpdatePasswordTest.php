<?php

namespace Tests\Unit\Http\Requests\Users;

use App\Auth\AuthManager;
use App\Http\Requests\Users\UpdatePassword;
use Tests\Helper\AuthHelper;
use Tests\Helper\ReflectionHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class UpdatePasswordTest extends TestCase
{
    use AuthHelper;
    use ReflectionHelper;
    use UsersHelper;

    private function getUpdatePassword(AuthManager $authManager = null): UpdatePassword
    {
        return new UpdatePassword($authManager ?: $this->createAuthManager());
    }

    public function testRules(): void
    {
        $this->assertEquals(
            [
                'password'        => ['required', 'string'],
                'currentPassword' => ['required', 'string', function () {
                }],
            ],
            $this->getUpdatePassword()->rules()
        );
    }

    public function testGetUserPassword(): void
    {
        $password = $this->getFaker()->password;
        $request = $this->getUpdatePassword();
        $request->offsetSet('password', $password);

        $this->assertEquals($password, $request->getUserPassword());
    }

    private function setUpCorrectCurrentPasswordTest(bool $validPassword = true): array
    {
        $currentPassword = $this->getFaker()->password;
        $user = $this->createUserModel();
        $authManager = $this->createAuthManager();
        $this->mockAuthManagerAuthenticatedUser($authManager, $user);
        $this->mockAuthManagerValidateCredentials($authManager, $validPassword, $user, $currentPassword);
        $request = $this->getUpdatePassword($authManager);

        return [$request, $currentPassword];
    }

    public function testCorrectCurrentPasswordRule(): void
    {
        /** @var UpdatePassword $request */
        [$request, $currentPassword] = $this->setUpCorrectCurrentPasswordTest();
        $rule = $this->runPrivateMethod($request, 'getCorrectCurrentPasswordRule');

        $this->assertTrue($rule('currentPassword', $currentPassword, function () {
        }));
    }

    public function testCorrectCurrentPasswordRuleWithIncorrectPassword(): void
    {
        /** @var UpdatePassword $request */
        [$request, $currentPassword] = $this->setUpCorrectCurrentPasswordTest(validPassword: false);
        $rule = $this->runPrivateMethod($request, 'getCorrectCurrentPasswordRule');
        $fail = '';

        $this->assertFalse($rule('currentPassword', $currentPassword, function (string $message) use (&$fail) {
            $fail = $message;
        }));
        $this->assertEquals('validation.invalid-password', $fail);
    }

    public function testCorrectCurrentPasswordRuleWithoutCurrentPassword(): void
    {
        /** @var UpdatePassword $request */
        [$request] = $this->setUpCorrectCurrentPasswordTest();
        $rule = $this->runPrivateMethod($request, 'getCorrectCurrentPasswordRule');

        $this->assertTrue($rule('currentPassword', '', function () {
        }));
    }
}
