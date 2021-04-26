<?php

namespace Tests\Unit\Http\Requests\Projects;

use App\Http\Requests\Projects\Invite;
use App\Http\Rules\RoleExists;
use App\Projects\MetaData\MetaDataManager;
use Tests\Helper\HttpHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\ReflectionHelper;
use Tests\TestCase;

/**
 * Class InviteTest
 *
 * @package Tests\Unit\Http\Requests\Projects
 */
final class InviteTest extends TestCase
{
    use HttpHelper;
    use ProjectHelper;
    use ReflectionHelper;

    //region Tests

    /**
     * @return void
     */
    public function testRules(): void
    {
        $roleExists = $this->createRoleExistsRule();

        $this->assertEquals(
            [
                'role'     => ['required', $roleExists],
                'email'    => ['required', 'email'],
                'metaData' => ['array', function () {
                }],
            ],
            $this->getInvite()->rules()
        );
    }

    /**
     * @return void
     */
    public function testGetRole(): void
    {
        $role = $this->createRoleModel();
        $roleExists = $this->createRoleExistsRule();
        $this->mockRoleExistsRuleGetRole($roleExists, $role);

        $this->assertEquals($role, $this->getInvite($roleExists)->getRole());
    }

    /**
     * @return void
     */
    public function testGetEmail(): void
    {
        $email = $this->getFaker()->safeEmail;
        $request = $this->getInvite();
        $request->offsetSet('email', $email);

        $this->assertEquals($email, $request->getEmail());
    }

    /**
     * @return void
     */
    public function testGetMetaData(): void
    {
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $request = $this->getInvite();
        $request->offsetSet('metaData', $metaData);

        $this->assertEquals($metaData, $request->getMetaData());
    }

    /**
     * @return void
     */
    public function testGetMetaDataWithoutMetaData(): void
    {
        $this->assertEquals([], $this->getInvite()->getMetaData());
    }

    /**
     * @return array
     */
    private function setUpMetaDataValidationRuleTest(bool $withValidMetaData = true): array
    {
        $argument = $this->getFaker()->word;
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $project = $this->createProjectModel();
        $validationMessageMetaDataElement = $this->getFaker()->word;
        $validationMessage = $this->getFaker()->word;
        $validationMessages = [$validationMessageMetaDataElement => [$validationMessage]];
        $metaDataManager = $this->createMetaDataManager();
        $this->mockMetaDataManagerValidateMetaData($metaDataManager, $withValidMetaData ? [] : $validationMessages, $project, $metaData);
        $route = $this->createRoute();
        $this->mockRouteParameter($route, $project, 'project', null);
        $request = $this->getInvite(null, $metaDataManager);
        $messageBag = $this->createMessageBag();
        $validator = $this->createValidator();
        $this->mockValidatorGetMessageBag($validator, $messageBag);
        $request->setValidator($validator);
        $request->setRouteResolver(fn () => $route);
        $metaDataValidationRule = $this->runPrivateMethod($request, 'getMetaDataValidationRule');

        return [$metaDataValidationRule, $argument, $metaData, $messageBag, $validationMessageMetaDataElement, $validationMessage];
    }

    /**
     * @return void
     */
    public function testMetaDataValidationRule(): void
    {
        [$metaDataValidationRule, $argument, $value] = $this->setUpMetaDataValidationRuleTest();

        $this->assertTrue($metaDataValidationRule($argument, $value, function () {
        }));
    }

    /**
     * @return void
     */
    public function testMetaDataValidationRuleWithoutMetaDataArray(): void
    {
        [$metaDataValidationRule, $argument] = $this->setUpMetaDataValidationRuleTest();
        $fail = function (string $input) use (&$errorMessage) {
            $errorMessage = $input;
        };

        $this->assertFalse($metaDataValidationRule($argument, $this->getFaker()->word, $fail));
        $this->assertEquals('validation.array', $errorMessage);
    }

    /**
     * @return void
     */
    public function testMetaDataValidationRuleWithInvalidMetaData(): void
    {
        [
            $metaDataValidationRule,
            $argument,
            $value,
            $messageBag,
            $validationMessageMetaDataElement,
            $validationMessage,
        ] = $this->setUpMetaDataValidationRuleTest(false);

        $this->assertFalse($metaDataValidationRule($argument, $value, function () {
        }));
        $messageBag
            ->shouldHaveReceived('merge')
            ->with([$validationMessageMetaDataElement => [\sprintf('validation.%s', $validationMessage)]])
            ->once();
    }

    //endregion

    /**
     * @param RoleExists|null      $roleExists
     * @param MetaDataManager|null $metaDataManager
     *
     * @return Invite
     */
    private function getInvite(RoleExists $roleExists = null, MetaDataManager $metaDataManager = null): Invite
    {
        return new Invite(
            $roleExists ?: $this->createRoleExistsRule(),
            $metaDataManager ?: $this->createMetaDataManager()
        );
    }
}
