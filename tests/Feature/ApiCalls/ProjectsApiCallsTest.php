<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\ProjectsController;
use App\Projects\Invites\InvitationModel;
use App\Projects\Invites\InvitationRepository;
use App\Projects\MemberModel;
use App\Projects\MemberRepository;
use App\Projects\MetaDataElementModel;
use App\Projects\MetaDataModel;
use App\Projects\ProjectModel;
use App\Projects\ProjectRepository;
use App\Projects\RoleModel;
use App\Projects\RoleRepository;
use App\Users\UserModel;
use Carbon\CarbonImmutable;
use LaravelDoctrine\Migrations\Testing\DatabaseMigrations;
use Tests\Feature\FeatureTestCase;
use Tests\Helper\ApiHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;

/**
 * Class ProjectsApiCallsTest
 *
 * @package Tests\Feature\ApiCalls
 */
final class ProjectsApiCallsTest extends FeatureTestCase
{
    use ApiHelper;
    use DatabaseMigrations;
    use ModelHelper;
    use ProjectHelper;
    use UsersHelper;

    //region Tests

    /**
     * @param bool $withAuthenticatedUser
     *
     * @return array
     */
    private function setUpCreateTest(bool $withAuthenticatedUser = true): array
    {
        $user = $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        $projectLabel = $this->getFaker()->word;
        $projectDescription = $this->getFaker()->words(3, true);

        $metaDataElementLabel = $this->getFaker()->word;
        $metaDataElementRequired = $this->getFaker()->boolean;
        $metaDataElementInList = $this->getFaker()->boolean;
        $metaDataName =  $this->getFaker()->word;
        $metaDataValue = $this->getFaker()->word;

        return [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
            $metaDataElementRequired,
            $metaDataElementInList,
            $user,
        ];
    }

    /**
     * @return void
     */
    public function testCreateProject(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
            $metaDataElementRequired,
            $metaDataElementInList,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'required' => $metaDataElementRequired,
                        'inList'   => $metaDataElementInList,
                        'type'     => 'string',
                    ],
                ],
                'metaData'         => [$metaDataName => $metaDataValue],
            ]
        );

        $response->assertStatus(201);
        /** @var ProjectModel $project */
        $project = $this->getConcreteProjectRepository()->findAll()->first();
        $response->assertJsonFragment([
            'project' => [
                'uuid'             => $project->getUuid(),
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'type'     => 'string',
                        'required' => $metaDataElementRequired,
                        'inList'   => $metaDataElementInList,
                    ],
                ],
                'roles'            => [
                    [
                        'uuid'  => $project->getRoles()->first()->getUuid(),
                        'label' => 'Owner',
                        'owner' => true,
                    ]
                ]
            ],
        ]);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithRole(): void
    {
        /** @var UserModel $user */
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
            $metaDataElementRequired,
            $metaDataElementInList,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'required' => $metaDataElementRequired,
                        'inList'   => $metaDataElementInList,
                        'type'     => 'string',
                    ],
                ],
                'metaData'         => [$metaDataName => $metaDataValue],
            ]
        );

        $project = $this->getConcreteProjectRepository()->findAll()->first();
        $role = $this->getConcreteRoleRepository()->findAll()->first();
        $this->assertEquals('Owner', $role->getLabel());
        $this->assertTrue($role->isOwner());
        $this->assertEquals($project, $role->getProject());
    }

    /**
     * @return void
     */
    public function testCreateProjectWithoutMetaData(): void
    {
        [$projectLabel, $projectDescription] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'       => $projectLabel,
                'description' => $projectDescription,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'metaDataElements' => ['validation.present'],
            'metaData'         => ['validation.present'],
        ]);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithoutOptionalMetaDataElementParameters(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'type'     => 'string',
                    ],
                ],
                'metaData'         => [$metaDataName => $metaDataValue],
            ]
        );

        $response->assertStatus(201);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithoutRequiredParameters(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
            $metaDataElementRequired,
            $metaDataElementInList,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'metaDataElements' => [
                    [
                        'required' => $metaDataElementRequired,
                        'inList'   => $metaDataElementInList,
                    ],
                ],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'label'                    => ['validation.required'],
            'description'              => ['validation.required'],
            'metaDataElements.0.name'  => ['validation.required'],
            'metaDataElements.0.label' => ['validation.required'],
            'metaDataElements.0.type'  => ['validation.required'],
        ]);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithoutRequiredMetaData(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'required' => true,
                        'type'     => 'string',
                    ],
                ],
                'metaData'         => [],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'metaData' => [$metaDataName => ['validation.required']],
        ]);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithNonExistingMetaDataField(): void
    {
        [$projectLabel, $projectDescription, $metaDataName] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [],
                'metaData'         => [$metaDataName => $this->getFaker()->word],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'metaData' => [$metaDataName => ['validation.not-existing']],
        ]);
    }
    /**
     * @return void
     */
    public function testCreateProjectWithValidNumericMetaData(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'type'     => 'numeric',
                    ]
                ],
                'metaData'         => [$metaDataName => $this->getFaker()->numberBetween()],
            ]
        );

        $response->assertStatus(201);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithValidEmailMetaData(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'type'     => 'email',
                    ]
                ],
                'metaData'         => [$metaDataName => $this->getFaker()->safeEmail],
            ]
        );

        $response->assertStatus(201);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithValidDateMetaData(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'type'     => 'date',
                    ]
                ],
                'metaData'         => [$metaDataName => $this->getFaker()->dateTime->format('Y-m-d H:i:s')],
            ]
        );

        $response->assertStatus(201);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithInvalidStringMetaData(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'type'     => 'string',
                    ]
                ],
                'metaData'         => [$metaDataName => $this->getFaker()->numberBetween()],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'metaData' => [$metaDataName => ['validation.string']],
        ]);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithInvalidNumericMetaData(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'type'     => 'numeric',
                    ]
                ],
                'metaData'         => [$metaDataName => $this->getFaker()->word],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'metaData' => [$metaDataName => ['validation.numeric']],
        ]);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithInvalidEmailMetaData(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'type'     => 'email',
                    ]
                ],
                'metaData'         => [$metaDataName => $this->getFaker()->word],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'metaData' => [$metaDataName => ['validation.email']],
        ]);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithInvalidDateMetaData(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
        ] = $this->setUpCreateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'type'     => 'date',
                    ]
                ],
                'metaData'         => [$metaDataName => $this->getFaker()->word],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'metaData' => [$metaDataName => ['validation.date']],
        ]);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithoutAuthenticatedUser(): void
    {
        [
            $projectLabel,
            $projectDescription,
            $metaDataName,
            $metaDataValue,
            $metaDataElementLabel,
            $metaDataElementRequired,
            $metaDataElementInList,
        ] = $this->setUpCreateTest(false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE),
            [
                'label'            => $projectLabel,
                'description'      => $projectDescription,
                'metaDataElements' => [
                    [
                        'name'     => $metaDataName,
                        'label'    => $metaDataElementLabel,
                        'required' => $metaDataElementRequired,
                        'inList'   => $metaDataElementInList,
                        'type'     => 'string',
                    ],
                ],
                'metaData'         => [$metaDataName => $metaDataValue],
            ]
        );

        $response->assertStatus(401);
    }

    /**
     * @param bool $withProjects
     * @param bool $withAuthenticatedUser
     *
     * @return array
     */
    private function setUpUsersProjectsTest(bool $withProjects = true, bool $withAuthenticatedUser = true): array
    {
        $project = $this->createProjectEntities()->first();
        $role = $this->createRoleEntities(1, [RoleModel::PROPERTY_PROJECT => $project])->first();
        $project->addRole($role);
        $user = $withProjects
            ? $this->createUserWithRole($role)
            : $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        return [$project, $role];
    }

    /**
     * @return void
     */
    public function testUsersProjectsWithProjects(): void
    {
        /**
         * @var ProjectModel $project
         * @var RoleModel    $role
         */
        [$project, $role] = $this->setUpUsersProjectsTest();

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_USERS_PROJECTS));

        $response->assertOk();
        $response->assertJsonFragment([
            'projects' => [
                [
                    'uuid'    => $role->getUuid(),
                    'label'   => $role->getLabel(),
                    'owner'   => $role->isOwner(),
                    'project' => [
                        'uuid'             => $project->getUuid(),
                        'label'            => $project->getLabel(),
                        'description'      => $project->getDescription(),
                        'roles'            => [
                            [
                                'uuid'  => $role->getUuid(),
                                'label' => $role->getLabel(),
                                'owner' => $role->isOwner(),
                            ],
                        ],
                        'metaDataElements' => [],
                    ],
                ],
            ]
        ]);
    }

    /**
     * @return void
     */
    public function testUsersProjectsWithoutProjects(): void
    {
        $this->setUpUsersProjectsTest(false);

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_USERS_PROJECTS));

        $response->assertOk();
        $response->assertJsonFragment(['projects' => []]);
    }

    /**
     * @return void
     */
    public function testUsersProjectsWithoutAuthenticatedUser(): void
    {
        $this->setUpUsersProjectsTest(true, false);

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_USERS_PROJECTS));

        $response->assertStatus(401);
    }

    /**
     * @param bool $withAuthenticatedUser
     * @param bool $withAuthorizedUser
     *
     * @return array
     */
    private function setUpShowProjectTest(bool $withAuthenticatedUser = true, bool $withAuthorizedUser = true): array
    {
        $project = $this->createProjectEntities()->first();
        $role = $this->createRoleEntities(1, [RoleModel::PROPERTY_PROJECT => $project])->first();
        $user = $withAuthorizedUser ? $this->createUserWithRole($role) : $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        return [$project];
    }

    /**
     * @return void
     */
    public function testShowProject(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpShowProjectTest();

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_SHOW, ['project' => $project->getUuid()]));

        $response->assertOk();
        $response->assertJsonFragment([
            'project' => [
                'uuid'             => $project->getUuid(),
                'label'            => $project->getLabel(),
                'description'      => $project->getDescription(),
                'roles'            => [],
                'metaDataElements' => [],
            ]
        ]);
    }

    /**
     * @return void
     */
    public function testShowProjectWithoutProject(): void
    {
        $this->setUpShowProjectTest();

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_SHOW, ['project' => $this->getFaker()->uuid]));

        $response->assertNotFound();
    }

    /**
     * @return void
     */
    public function testShowProjectWithoutAuthenticatedUser(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpShowProjectTest(false);

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_SHOW, ['project' => $project->getUuid()]));

        $response->assertStatus(401);
    }

    /**
     * @return void
     */
    public function testShowProjectWithoutAuthorizedUser(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpShowProjectTest(true, false);

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_SHOW, ['project' => $project->getUuid()]));

        $response->assertStatus(403);
    }

    /**
     * @param bool $withMembers
     * @param bool $withRoles
     * @param bool $withMetaData
     * @param bool $withAuthenticatedUser
     * @param bool $withAuthorizedUser
     * @param bool $withOwner
     *
     * @return array
     */
    private function setUpMembersTest(
        bool $withMembers = true,
        bool $withRoles = true,
        bool $withMetaData = true,
        bool $withAuthenticatedUser = true,
        bool $withAuthorizedUser = true,
        bool $withOwner = false
    ): array {
        if ($withAuthorizedUser) {
            $role = $this->createRoleWithPermission($this->getProjectsMembersShowPermission());
        } elseif ($withOwner) {
            $role = $this->createOwnerRole();
        } else {
            $role = $this->createRoleEntities()->first();
        }
        $authenticatedUser = $this->createUserWithRole($role);
        if ($withAuthenticatedUser) {
            $this->actingAs($authenticatedUser);
        }
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $user = $this->createUserEntities()->first();
        $project = $role->getProject();
        if ($withMembers) {
            $member = $this->createMemberEntities(
                1,
                [
                    MemberModel::PROPERTY_USER => $user,
                    MemberModel::PROPERTY_ROLE => $role,
                    MemberModel::PROPERTY_META_DATA => \json_encode($withMetaData ? $metaData : []),
                ]
            )->first();
            $role->addMember($member);
            $user->addMember($member);
        }
        if ($withRoles) {
            $project->addRole($role);
        }

        return [$project, $user, $metaData];
    }

    /**
     * @return void
     */
    public function testMembers(): void
    {
        /**
         * @var ProjectModel  $project
         * @var UserModel     $user
         */
        [$project, $user, $metaData] = $this->setUpMembersTest();

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $project->getUuid()])
        );

        $response->assertOk();
        $response->assertJsonFragment([
            'members' => [
                [
                    UserModel::PROPERTY_UUID        => $user->getUuid(),
                    UserModel::PROPERTY_EMAIL       => $user->getEmail(),
                    MemberModel::PROPERTY_META_DATA => $metaData,
                ],
            ]
        ]);
    }

    /**
     * @return void
     */
    public function testMembersWithoutMembers(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpMembersTest(false);

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $project->getUuid()])
        );

        $response->assertOk();
        $response->assertJsonFragment(['members' => []]);
    }

    /**
     * @return void
     */
    public function testMembersWithoutRoles(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpMembersTest(true, false);

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $project->getUuid()])
        );

        $response->assertOk();
        $response->assertJsonFragment(['members' => []]);
    }

    /**
     * @return void
     */
    public function testMembersWithoutMetaData(): void
    {
        /**
         * @var ProjectModel  $project
         * @var UserModel     $user
         */
        [$project, $user] = $this->setUpMembersTest(true, true, false);

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $project->getUuid()])
        );

        $response->assertOk();
        $response->assertJsonFragment([
           'members' => [
               [
                   UserModel::PROPERTY_UUID        => $user->getUuid(),
                   UserModel::PROPERTY_EMAIL       => $user->getEmail(),
                   MemberModel::PROPERTY_META_DATA => [],
               ],
           ]
       ]);
    }

    /**
     * @return void
     */
    public function testMembersWithoutProject(): void
    {
        $this->setUpMembersTest();

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $this->getFaker()->uuid])
        );

        $response->assertNotFound();
    }

    /**
     * @return void
     */
    public function testMembersWithoutAuthenticatedUser(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpMembersTest(true, true, true, false);

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $project->getUuid()])
        );

        $response->assertStatus(401);
    }

    /**
     * @return void
     */
    public function testMembersWithoutAuthorizedUser(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpMembersTest(true, true, true, true, false);

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $project->getUuid()])
        );

        $response->assertStatus(403);
    }

    /**
     * @return void
     */
    public function testMembersWithoutOwner(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpMembersTest(
            true,
            true,
            true,
            true,
            false,
            true
        );

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $project->getUuid()])
        );

        $response->assertOk();
    }

    private function setUpInviteTest(
        bool $withExistingMetaData = true,
        bool $withRequiredMetaData = true,
        bool $validMetaData = true,
        string $type = 'string',
        bool $withAuthenticatedUser = true,
        bool $withAuthorizedUser = true,
        bool $withOwner = false
    ): array {
        if ($withAuthorizedUser) {
            $role = $this->createRoleWithPermission($this->getInvitationsManagementPermission());
        } elseif ($withOwner) {
            $role = $this->createOwnerRole();
        } else {
            $role = $this->createRoleEntities()->first();
        }
        $metaDataElement = $this->createMetaDataElementEntities(
            1,
            [
                MetaDataElementModel::PROPERTY_PROJECT  => $role->getProject(),
                MetaDataElementModel::PROPERTY_REQUIRED => !$withRequiredMetaData,
                MetaDataElementModel::PROPERTY_TYPE     => $type,
            ]
        )->first();
        $role->getProject()->addMetaDataElement($metaDataElement);
        $user = $this->createUserWithRole($role);
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }
        $email = $this->getFaker()->safeEmail;
        $metaDataName = $metaDataElement->getName() . ($withExistingMetaData ? '' : $this->getFaker()->word);
        $metaData = $withRequiredMetaData
            ? [$metaDataName => $validMetaData ? $this->getValidMetaData($type) : $this->getInvalidMetaData($type)]
            : [];
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
        $validUntil = $now->addDays(3);

        return [$email, $role, $metaData, $validUntil, $metaDataName];
    }

    /**
     * @param string $type
     *
     * @return \DateTime|int|string
     */
    private function getValidMetaData(string $type)
    {
        switch ($type) {
            case 'email':
                return $this->getFaker()->safeEmail;
            case 'numeric':
                return $this->getFaker()->numberBetween();
            case 'date':
                return $this->getFaker()->dateTime;
            default:
            case 'string':
                return $this->getFaker()->word;
        }
    }

    /**
     * @param string $type
     *
     * @return \DateTime|int|string
     */
    private function getInvalidMetaData(string $type)
    {
        switch ($type) {
            case 'email':
            case 'numeric':
            case 'date':
                return $this->getFaker()->word;
            default:
            case 'string':
                return $this->getFaker()->numberBetween();
        }
    }

    /**
     * @return void
     */
    public function testInvite(): void
    {
        /**
         * @var RoleModel       $role
         * @var CarbonImmutable $validUntil
         */
        [$email, $role, $metaData, $validUntil] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'role'     => $role->getUuid(),
                'metaData' => $metaData,
            ]
        );

        $response->assertCreated();
        $invitation = $this->getInvitationRepository()->findAll()->first();
        $response->assertJsonFragment([
            'invitation' => [
                'uuid'       => $invitation->getUuid(),
                'email'      => $email,
                'role'       => $role->toArray(true),
                'metaData'   => $metaData,
                'validUntil' => $validUntil->format('Y-m-d H:i:s'),
                'acceptedAt' => null,
                'declinedAt' => null,
            ]
        ]);
    }

    /**
     * @return void
     */
    public function testInviteWithoutEmail(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['email' => ['validation.required']]);
    }

    /**
     * @return void
     */
    public function testInviteWithoutExistingRole(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $this->getFaker()->uuid]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertNotFound();
    }

    /**
     * @return void
     */
    public function testInviteWithInvalidMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $role] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $this->getFaker()->word,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => ['validation.array']]);
    }

    /**
     * @return void
     */
    public function testInviteWithoutExistingMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.not-existing', $metaDataName)]]);
    }

    /**
     * @return void
     */
    public function testInviteWithoutRequiredMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.required', $metaDataName)]]);
    }

    /**
     * @return void
     */
    public function testInviteWithInvalidStringMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, true, false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.string', $metaDataName)]]);
    }

    /**
     * @return void
     */
    public function testInviteWithInvalidEmailMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, true, false, 'email');

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.email', $metaDataName)]]);
    }

    /**
     * @return void
     */
    public function testInviteWithInvalidNumericMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, true, false, 'numeric');

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.numeric', $metaDataName)]]);
    }

    /**
     * @return void
     */
    public function testInviteWithInvalidDateMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, true, false, 'date');

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.date', $metaDataName)]]);
    }

    /**
     * @return void
     */
    public function testInviteWithoutAuthenticatedUser(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData] = $this->setUpInviteTest(
            true,
            true,
            true,
            'string',
            false
        );

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(401);
    }

    /**
     * @return void
     */
    public function testInviteWithoutAuthorizedUser(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData] = $this->setUpInviteTest(
            true,
            true,
            true,
            'string',
            true,
            false
        );

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(403);
    }

    /**
     * @return void
     */
    public function testInviteWithOwnerRole(): void
    {
        /** @var RoleModel $role */
        [$email, $role, $metaData] = $this->setUpInviteTest(
            true,
            true,
            true,
            'string',
            true,
            false,
            true
        );

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE, ['role' => $role->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertCreated();
    }

    private function setUpAcceptInvitationTest(
        bool $withRequiredMetaData = false,
        string $metaDataType = 'string',
        bool $withValidInvitation = true,
        bool $alreadyAccepted = false,
        bool $declinedInvitation = false,
        bool $withAuthenticatedUser = true,
        bool $invitationBelongsToUser = true,
        bool $alreadyMember = false
    ): array {
        $user = $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }
        $invitation = $this->createInvitationEntities(
            1,
            [
                InvitationModel::PROPERTY_EMAIl       => ($invitationBelongsToUser ? '' : $this->getFaker()->word) . $user->getEmail(),
                InvitationModel::PROPERTY_VALID_UNTIL => $withValidInvitation ? (new CarbonImmutable())->addDay() : (new CarbonImmutable())->subDay(),
                InvitationModel::PROPERTY_ACCEPTED_AT => $alreadyAccepted ? new CarbonImmutable() : null,
                InvitationModel::PROPERTY_DECLINED_AT => $declinedInvitation ? new CarbonImmutable() : null,
            ]
        )->first();
        $metaDataElement = $this->createMetaDataElementEntities(
            1,
            [
                MetaDataElementModel::PROPERTY_PROJECT  => $invitation->getRole()->getProject(),
                MetaDataElementModel::PROPERTY_REQUIRED => $withRequiredMetaData,
                MetaDataElementModel::PROPERTY_TYPE     => $metaDataType,
            ]
        )->first();
        $invitation->getRole()->getProject()->addMetaDataElement($metaDataElement);
        if ($alreadyMember) {
            $member = $this->createMemberEntities(1, [MemberModel::PROPERTY_USER => $user, MemberModel::PROPERTY_ROLE => $invitation->getRole()])->first();
            $user->addMember($member);
        }

        return [$invitation, $user, $metaDataElement->getName()];
    }

    public function testAcceptInvitationWithoutMetaData(): void
    {
        [$invitation, $user] = $this->setUpAcceptInvitationTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertCreated();
        $member = $this->getMemberRepository()->findAll()->first();
        $this->assertEquals($invitation->getRole(), $member->getRole());
        $this->assertEquals($user, $member->getUser());
    }

    public function testAcceptInvitationWithMetaData(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest();
        $metaDataValue = $this->getFaker()->word;

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            [
                'metaData' => [$metaDataName => $metaDataValue],
            ]
        );

        $response->assertCreated();
        $member = $this->getMemberRepository()->findAll()->first();
        $this->assertEquals([$metaDataName => $metaDataValue], $member->getMetaData());
    }

    public function testAcceptInvitationWithoutInvitationFound(): void
    {
        $this->setUpAcceptInvitationTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $this->getFaker()->uuid])
        );

        $response->assertNotFound();
    }

    public function testAcceptInvitationWithInvalidMetaData(): void
    {
        [$invitation] = $this->setUpAcceptInvitationTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => $this->getFaker()->word]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => ['validation.array']]);
    }

    public function testAcceptInvitationWithNonExistingMetaData(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest();
        $metaDataName = $metaDataName . $this->getFaker()->word;

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.not-existing', $metaDataName)]]);
    }

    public function testAcceptInvitationWithoutRequiredMetaData(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(true);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.required', $metaDataName)]]);
    }

    public function testAcceptInvitationWithInvalidStringMetaData(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->numberBetween()]]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.string', $metaDataName)]]);
    }

    public function testAcceptInvitationWithInvalidNumberMetaData(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(false, 'numeric');

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.numeric', $metaDataName)]]);
    }

    public function testAcceptInvitationWithInvalidEmailMetaData(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(false, 'email');

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.email', $metaDataName)]]);
    }

    public function testAcceptInvitationWithInvalidDateMetaData(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(false, 'date');

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.date', $metaDataName)]]);
    }

    public function testAcceptInvitationWithInvalidWithExpiredInvitation(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(false, 'string', false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(400);
    }

    public function testAcceptInvitationWithInvalidWithAlreadyAccepted(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(false, 'string', true, true);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(400);
    }

    public function testAcceptInvitationWithInvalidWithDeclinedInvitation(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(false, 'string', true, false, true);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(400);
    }

    public function testAcceptInvitationWithInvalidWithAuthenticatedUser(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(
            false,
            'string',
            true,
            false,
            false,
            false
        );

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(401);
    }

    public function testAcceptInvitationWithInvalidWithInvitationNotBelongingToUser(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(
            false,
            'string',
            true,
            false,
            false,
            true,
            false
        );

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(403);
    }

    public function testAcceptInvitationWithInvalidWithUserAlreadyMember(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(
            false,
            'string',
            true,
            false,
            false,
            true,
            true,
            true
        );

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(403);
    }

    //endregion

    private function getConcreteProjectRepository(): ProjectRepository
    {
        return $this->app->get(ProjectRepository::class);
    }

    private function getConcreteRoleRepository(): RoleRepository
    {
        return $this->app->get(RoleRepository::class);
    }

    private function getInvitationRepository(): InvitationRepository
    {
        return $this->app->get(InvitationRepository::class);
    }

    private function getMemberRepository(): MemberRepository
    {
        return $this->app->get(MemberRepository::class);
    }
}
