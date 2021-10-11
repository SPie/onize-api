<?php

namespace Tests\Unit\Http\Rules;

use App\Http\Rules\UserExistsAndIsMember;
use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserManager;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class UserExistsAndIsMemberTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    private function getUserExistsAndIsMember(UserManager $userManager = null): UserExistsAndIsMember
    {
        return new UserExistsAndIsMember($userManager ?: $this->createUserManager());
    }

    public function testMessage(): void
    {
        $this->assertEquals('validation.user-not-found', $this->getUserExistsAndIsMember()->message());
    }

    private function setUpPassesTest(
        bool $withUser = true,
        bool $withProject = false,
        bool $matchingProject = true
    ): array {
        $uuid = $this->getFaker()->uuid;
        $project = $this->createProjectModel();
        $user = $this->createUserModel();
        $this->mockUserModelIsMemberOfProject($user, $matchingProject, $project);
        $userManager = $this->createUserManager();
        $this->mockUserManagerGetUserByUuid($userManager, $withUser ? $user : new ModelNotFoundException(), $uuid);
        $rule = $this->getUserExistsAndIsMember($userManager);
        if ($withProject) {
            $rule->setProject($project);
        }

        return [$rule, $uuid, $user];
    }

    public function testPasses(): void
    {
        /** @var UserExistsAndIsMember $rule */
        [$rule, $uuid, $user] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, $uuid));
        $this->assertEquals($user, $rule->getUser());
    }

    public function testPassesWithoutUser(): void
    {
        /** @var UserExistsAndIsMember $rule */
        [$rule, $uuid] = $this->setUpPassesTest(withUser: false);

        $this->assertFalse($rule->passes($this->getFaker()->word, $uuid));
    }

    public function testPassesWithProject(): void
    {
        /** @var UserExistsAndIsMember $rule */
        [$rule, $uuid] = $this->setUpPassesTest(withProject: true);

        $this->assertTrue($rule->passes($this->getFaker()->word, $uuid));
    }

    public function testPassesWithoutMatchingProject(): void
    {
        /** @var UserExistsAndIsMember $rule */
        [$rule, $uuid] = $this->setUpPassesTest(withProject: true, matchingProject: false);

        $this->assertFalse($rule->passes($this->getFaker()->word, $uuid));
    }
}
