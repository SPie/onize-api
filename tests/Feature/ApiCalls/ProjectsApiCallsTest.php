<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\InvitationsController;
use App\Http\Controllers\ProjectsController;
use App\Projects\Invites\InvitationModel;
use App\Projects\Invites\InvitationRepository;
use App\Projects\MemberModel;
use App\Projects\MemberRepository;
use App\Projects\MetaDataElementModel;
use App\Projects\PermissionModel;
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

final class ProjectsApiCallsTest extends FeatureTestCase
{
    use ApiHelper;
    use DatabaseMigrations;
    use ModelHelper;
    use ProjectHelper;
    use UsersHelper;

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

    public function testUsersProjectsWithoutProjects(): void
    {
        $this->setUpUsersProjectsTest(false);

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_USERS_PROJECTS));

        $response->assertOk();
        $response->assertJsonFragment(['projects' => []]);
    }

    public function testUsersProjectsWithoutAuthenticatedUser(): void
    {
        $this->setUpUsersProjectsTest(true, false);

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_USERS_PROJECTS));

        $response->assertStatus(401);
    }

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

    public function testShowProjectWithoutProject(): void
    {
        $this->setUpShowProjectTest();

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_SHOW, ['project' => $this->getFaker()->uuid]));

        $response->assertNotFound();
    }

    public function testShowProjectWithoutAuthenticatedUser(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpShowProjectTest(false);

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_SHOW, ['project' => $project->getUuid()]));

        $response->assertStatus(401);
    }

    public function testShowProjectWithoutAuthorizedUser(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpShowProjectTest(true, false);

        $response = $this->doApiCall('GET', $this->getUrl(ProjectsController::ROUTE_NAME_SHOW, ['project' => $project->getUuid()]));

        $response->assertStatus(403);
    }

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

    public function testMembersWithoutProject(): void
    {
        $this->setUpMembersTest();

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $this->getFaker()->uuid])
        );

        $response->assertNotFound();
    }

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
        bool $withOwner = false,
        bool $roleProjectAllowed = true
    ): array {
        if ($withAuthorizedUser) {
            $role = $this->createRoleWithPermission($this->getInvitationsManagementPermission());
        } elseif ($withOwner) {
            $role = $this->createOwnerRole();
        } else {
            $role = $this->createRoleEntities()->first();
        }
        $otherRole = $this->createRoleEntities()->first();
        $metaDataElement = $this->createMetaDataElementEntities(
            1,
            [
                MetaDataElementModel::PROPERTY_PROJECT  => $roleProjectAllowed ? $role->getProject() : $otherRole->getProject(),
                MetaDataElementModel::PROPERTY_REQUIRED => !$withRequiredMetaData,
                MetaDataElementModel::PROPERTY_TYPE     => $type,
            ]
        )->first();
        $role->getProject()->addMetaDataElement($metaDataElement);
        $otherRole->getProject()->addMetaDataElement($metaDataElement);
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

        return [$email, $role->getProject(), $roleProjectAllowed ? $role : $otherRole, $metaData, $validUntil, $metaDataName];
    }

    /**
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

    public function testInvite(): void
    {
        /**
         * @var RoleModel       $role
         * @var CarbonImmutable $validUntil
         */
        [$email, $project, $role, $metaData, $validUntil] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
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

    public function testInviteWithoutExistingRole(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $this->getFaker()->uuid,
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['role' => ['validation.role-not-found']]);
    }

    public function testInviteWithoutRole(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['role' => ['validation.required']]);
    }

    public function testInviteWithoutEmail(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['email' => ['validation.required']]);
    }

    public function testInviteWithoutExistingProject(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $this->getFaker()->uuid]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertNotFound();
    }

    public function testInviteWithInvalidMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role] = $this->setUpInviteTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $this->getFaker()->word,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => ['validation.array']]);
    }

    public function testInviteWithoutExistingMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.not-existing', $metaDataName)]]);
    }

    public function testInviteWithoutRequiredMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.required', $metaDataName)]]);
    }

    public function testInviteWithInvalidStringMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, true, false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.string', $metaDataName)]]);
    }

    public function testInviteWithInvalidEmailMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, true, false, 'email');

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.email', $metaDataName)]]);
    }

    public function testInviteWithInvalidNumericMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, true, false, 'numeric');

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.numeric', $metaDataName)]]);
    }

    public function testInviteWithInvalidDateMetaData(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData, $validUntil, $metaDataName] = $this->setUpInviteTest(true, true, false, 'date');

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.date', $metaDataName)]]);
    }

    public function testInviteWithoutAuthenticatedUser(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData] = $this->setUpInviteTest(
            true,
            true,
            true,
            'string',
            false
        );

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(401);
    }

    public function testInviteWithoutAuthorizedUser(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData] = $this->setUpInviteTest(
            true,
            true,
            true,
            'string',
            true,
            false
        );

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(403);
    }

    public function testInviteWithOwnerRole(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData] = $this->setUpInviteTest(
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
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertCreated();
    }

    public function testInviteWithRoleProjectNotAllowed(): void
    {
        /** @var RoleModel $role */
        [$email, $project, $role, $metaData] = $this->setUpInviteTest(roleProjectAllowed: false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_INVITE, ['project' => $project->getUuid()]),
            [
                'role'     => $role->getUuid(),
                'email'    => $email,
                'metaData' => $metaData,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['role' => ['validation.role-not-found']]);
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
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
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

        return [$invitation, $user, $metaDataElement->getName(), $now];
    }

    public function testAcceptInvitationWithoutMetaData(): void
    {
        [$invitation, $user, $metaDataName, $now] = $this->setUpAcceptInvitationTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertCreated();
        $member = $this->getMemberRepository()->findAll()->first();
        $this->assertEquals($invitation->getRole(), $member->getRole());
        $this->assertEquals($user, $member->getUser());
        $this->assertEquals($now, $invitation->getAcceptedAt());
    }

    public function testAcceptInvitationWithMetaData(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest();
        $metaDataValue = $this->getFaker()->word;

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $this->getFaker()->uuid])
        );

        $response->assertNotFound();
    }

    public function testAcceptInvitationWithInvalidMetaData(): void
    {
        [$invitation] = $this->setUpAcceptInvitationTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['metaData' => [\sprintf('%s.validation.required', $metaDataName)]]);
    }

    public function testAcceptInvitationWithInvalidStringMetaData(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(400);
    }

    public function testAcceptInvitationWithInvalidWithAlreadyAccepted(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(false, 'string', true, true);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(400);
    }

    public function testAcceptInvitationWithInvalidWithDeclinedInvitation(): void
    {
        [$invitation, $user, $metaDataName] = $this->setUpAcceptInvitationTest(false, 'string', true, false, true);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
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
            $this->getUrl(InvitationsController::ROUTE_NAME_ACCEPT_INVITATION, ['invitation' => $invitation->getUuid()]),
            ['metaData' => [$metaDataName => $this->getFaker()->word]]
        );

        $response->assertStatus(403);
    }

    private function setUpDeclineInvitationTest(
        bool $withDeclinedInvitation = false,
        bool $withExpiredInvitation = false,
        bool $withAcceptedInvitation = false,
        bool $withInvitedUser = true,
        bool $withPermission = true,
        bool $withMember = true
    ): array {
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);

        if ($withPermission) {
            $role = $this->createRoleWithPermission($this->getConcretePermission(PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT));
        } else {
            $role = $this->createRoleEntities()->first();
        }
        if ($withMember && !$withInvitedUser) {
            $user = $this->createUserWithRole($role);
        } else {
            $user = $this->createUserEntities()->first();
        }
        $this->actingAs($user);

        $invitation = $this->createInvitationEntities(
            1,
            [
                InvitationModel::PROPERTY_ROLE        => $role,
                InvitationModel::PROPERTY_EMAIl       =>  ($withInvitedUser ? '' : $this->getFaker()->word) . $user->getEmail(),
                InvitationModel::PROPERTY_DECLINED_AT => $withDeclinedInvitation ? $now : null,
                InvitationModel::PROPERTY_VALID_UNTIL => $withExpiredInvitation ? $now->subDay() : $now->addDay(),
                InvitationModel::PROPERTY_ACCEPTED_AT => $withAcceptedInvitation ? $now : null
            ]
        )->first();
        $role->addInvitation($invitation);

        return [$invitation];
    }

    public function testDeclineInvitationByInvitedUser(): void
    {
        [$invitation] = $this->setUpDeclineInvitationTest();

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(InvitationsController::ROUTE_NAME_DECLINE_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertNoContent();
        $this->assertNotEmpty($invitation->getDeclinedAt());
    }

    public function testDeclineInvitationWithoutExistingInvitation(): void
    {
        $this->setUpDeclineInvitationTest();

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(InvitationsController::ROUTE_NAME_DECLINE_INVITATION, ['invitation' => $this->getFaker()->uuid])
        );

        $response->assertNotFound();
    }

    public function testDeclineInvitationWithAlreadyDeclinedInvitation(): void
    {
        [$invitation] = $this->setUpDeclineInvitationTest(withDeclinedInvitation: true);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(InvitationsController::ROUTE_NAME_DECLINE_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertStatus(400);
    }

    public function testDeclineInvitationWithExpiredException(): void
    {
        [$invitation] = $this->setUpDeclineInvitationTest(withExpiredInvitation: true);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(InvitationsController::ROUTE_NAME_DECLINE_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertStatus(400);
    }

    public function testDeclineInvitationWithAlreadyAcceptedInvitation(): void
    {
        [$invitation] = $this->setUpDeclineInvitationTest(withAcceptedInvitation: true);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(InvitationsController::ROUTE_NAME_DECLINE_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertStatus(400);
    }

    public function testDeclineInvitationByProjectMemberWithPermission(): void
    {
        [$invitation] = $this->setUpDeclineInvitationTest(withInvitedUser: false);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(InvitationsController::ROUTE_NAME_DECLINE_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertNoContent();
        $this->assertNotEmpty($invitation->getDeclinedAt());
    }

    public function testDeclineInvitationByProjectMemberWithoutPermission(): void
    {
        [$invitation] = $this->setUpDeclineInvitationTest(withInvitedUser: false, withPermission: false);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(InvitationsController::ROUTE_NAME_DECLINE_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertStatus(403);
    }

    public function testDeclineInvitationWithoutMemberAndInvitedUser(): void
    {
        [$invitation] = $this->setUpDeclineInvitationTest(withInvitedUser: false, withMember: false);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(InvitationsController::ROUTE_NAME_DECLINE_INVITATION, ['invitation' => $invitation->getUuid()])
        );

        $response->assertStatus(403);
    }

    private function setUpRemoveMemberTest(
        bool $userIsMember = true,
        bool $withAuthenticatedUser = true,
        bool $withAuthorizedUser = true,
        bool $memberIsOwner = false,
        bool $userIsOwner = false
    ): array {
        if ($userIsOwner) {
            $role = $this->createOwnerRole();
        } elseif ($withAuthorizedUser) {
            $role = $this->createRoleWithPermission($this->getConcretePermission(PermissionModel::PERMISSION_PROJECTS_MEMBER_MANAGEMENT));
        } else {
            $role = $this->createRoleEntities()->first();
        }
        $role->getProject()->addRole($role);
        $user = $this->createUserWithRole($role);
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        $memberRole = $this->createRoleEntities(
            1,
            [
                RoleModel::PROPERTY_PROJECT => $userIsMember ? $role->getProject() : $this->createProjectEntities()->first(),
                RoleModel::PROPERTY_OWNER   => $memberIsOwner
            ]
        )->first();
        $memberRole->getProject()->addRole($memberRole);
        $memberUser = $this->createUserEntities()->first();
        $member = $this->createMemberEntities(1, [MemberModel::PROPERTY_USER => $memberUser, MemberModel::PROPERTY_ROLE => $memberRole])->first();
        $memberUser->addMember($member);
        $memberRole->addMember($member);

        return [$role->getProject(), $memberUser, $member];
    }

    public function testRemoveMember(): void
    {
        [$project, $user, $member] = $this->setUpRemoveMemberTest();

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(
                ProjectsController::ROUTE_NAME_REMOVE_MEMBER,
                ['project' => $project->getUuid(), 'user' => $user->getUuid()]
            )
        );

        $response->assertNoContent();
        $this->assertNotEmpty($member->getDeletedAt());
    }

    public function testRemoveMemberWithUserNotMember(): void
    {
        [$project, $user] = $this->setUpRemoveMemberTest(userIsMember: false);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(
                ProjectsController::ROUTE_NAME_REMOVE_MEMBER,
                ['project' => $project->getUuid(), 'user' => $user->getUuid()]
            )
        );

        $response->assertStatus(400);
    }

    public function testRemoveMemberWithoutAuthenticatedUser(): void
    {
        [$project, $user] = $this->setUpRemoveMemberTest(withAuthenticatedUser: false);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(
                ProjectsController::ROUTE_NAME_REMOVE_MEMBER,
                ['project' => $project->getUuid(), 'user' => $user->getUuid()]
            )
        );

        $response->assertStatus(401);
    }

    public function testRemoveMemberWithoutAuthorizedUser(): void
    {
        [$project, $user] = $this->setUpRemoveMemberTest(withAuthorizedUser: false);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(
                ProjectsController::ROUTE_NAME_REMOVE_MEMBER,
                ['project' => $project->getUuid(), 'user' => $user->getUuid()]
            )
        );

        $response->assertStatus(403);
    }

    public function testRemoveMemberWithMemberIsOwner(): void
    {
        [$project, $user] = $this->setUpRemoveMemberTest(memberIsOwner: true);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(
                ProjectsController::ROUTE_NAME_REMOVE_MEMBER,
                ['project' => $project->getUuid(), 'user' => $user->getUuid()]
            )
        );

        $response->assertStatus(403);
    }

    public function testRemoveMemberWithMemberIsOwnerAndAuthenticatedUserIsOwner(): void
    {
        [$project, $user] = $this->setUpRemoveMemberTest(memberIsOwner: true, userIsOwner: true);

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(
                ProjectsController::ROUTE_NAME_REMOVE_MEMBER,
                ['project' => $project->getUuid(), 'user' => $user->getUuid()]
            )
        );

        $response->assertNoContent();
    }

    public function testRemoveMemberWithoutProject(): void
    {
        [$project, $user] = $this->setUpRemoveMemberTest();

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(
                ProjectsController::ROUTE_NAME_REMOVE_MEMBER,
                ['project' => $this->getFaker()->uuid, 'user' => $user->getUuid()]
            )
        );

        $response->assertNotFound();
    }

    public function testRemoveMemberWithoutUser(): void
    {
        [$project] = $this->setUpRemoveMemberTest();

        $response = $this->doApiCall(
            'DELETE',
            $this->getUrl(
                ProjectsController::ROUTE_NAME_REMOVE_MEMBER,
                ['project' => $project->getUuid(), 'user' => $this->getFaker()->uuid]
            )
        );

        $response->assertNotFound();
    }

    private function setUpCreateRoleTest(
        bool $withAuthenticatedUser = true,
        bool $withAuthorizedUser = true,
        bool $withOwner = false
    ): array {
        if ($withAuthorizedUser) {
            $role = $this->createRoleWithPermission($this->getConcretePermission(PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT));
        } elseif ($withOwner) {
            $role = $this->createOwnerRole();
        } else {
            $role = $this->createRoleEntities()->first();
        }
        $user = $this->createUserWithRole($role);
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        $label = $this->getFaker()->word;

        $permissionPool = [
            PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT,
            PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT,
            PermissionModel::PERMISSION_PROJECTS_MEMBERS_SHOW,
        ];

        return [$role->getProject(), $label, $permissionPool[\mt_rand(0, 2)]];
    }

    public function testCreateRole(): void
    {
        [$project, $label, $permission] = $this->setUpCreateRoleTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE_ROLE, ['project' => $project->getUuid()]),
            [
                'label' => $label,
                'permissions' => [$permission],
            ]
        );

        $response->assertCreated();
        $role = $this->getConcreteRoleRepository()->findAll()->get(1);
        $this->assertEquals($label, $role->getLabel());
        $this->assertEquals($project, $role->getProject());
        $this->assertEquals($this->getConcretePermission($permission), $role->getPermissions()->first());
        $response->assertJsonFragment([
            'role' => [
                'uuid'  => $role->getUuid(),
                'label' => $label,
                'owner' => false,
            ]
        ]);
    }

    public function testCreateRoleWithoutFoundProject(): void
    {
        [$project, $label, $permission] = $this->setUpCreateRoleTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE_ROLE, ['project' => $this->getFaker()->uuid]),
            [
                'label' => $label,
                'permissions' => [$permission],
            ]
        );

        $response->assertNotFound();
    }

    public function testCreateRoleWithoutLabel(): void
    {
        [$project, $label, $permission] = $this->setUpCreateRoleTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE_ROLE, ['project' => $project->getUuid()]),
            [
                'permissions' => [$permission],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'label' => ['validation.required']
        ]);
    }

    public function testCreateRoleWithInvalidLabel(): void
    {
        [$project, $label, $permission] = $this->setUpCreateRoleTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE_ROLE, ['project' => $project->getUuid()]),
            [
                'label' => $this->getFaker()->numberBetween(1),
                'permissions' => [$permission],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['label' => ['validation.string']]);
    }

    public function testCreateRoleWithoutPermissions(): void
    {
        [$project, $label, $permission] = $this->setUpCreateRoleTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE_ROLE, ['project' => $project->getUuid()]),
            [
                'label' => $label,
            ]
        );

        $response->assertCreated();
        $role = $this->getConcreteRoleRepository()->findAll()->get(1);
        $this->assertTrue($role->getPermissions()->isEmpty());
    }

    public function testCreateRoleWithoutFoundRole(): void
    {
        [$project, $label] = $this->setUpCreateRoleTest();
        $permission = $this->getFaker()->word;

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE_ROLE, ['project' => $project->getUuid()]),
            [
                'label' => $label,
                'permissions' => [$permission],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['permissions' => ['validation.permissions-not-found:' . $permission]]);
    }

    public function testCreateRoleWithoutAuthenticatedUser(): void
    {
        [$project, $label, $permission] = $this->setUpCreateRoleTest(withAuthenticatedUser: false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE_ROLE, ['project' => $project->getUuid()]),
            [
                'label' => $label,
                'permissions' => [$permission],
            ]
        );

        $response->assertStatus(401);
    }

    public function testCreateRoleWithoutAuthorizedUser(): void
    {
        [$project, $label, $permission] = $this->setUpCreateRoleTest(withAuthorizedUser: false);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE_ROLE, ['project' => $project->getUuid()]),
            [
                'label' => $label,
                'permissions' => [$permission],
            ]
        );

        $response->assertStatus(403);
    }

    public function testCreateRoleWithOwner(): void
    {
        [$project, $label, $permission] = $this->setUpCreateRoleTest(withAuthorizedUser: false, withOwner: true);

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(ProjectsController::ROUTE_NAME_CREATE_ROLE, ['project' => $project->getUuid()]),
            [
                'label' => $label,
                'permissions' => [$permission],
            ]
        );

        $response->assertCreated();
    }

    private function setUpChangeRoleTest(
        bool $roleInProject = true,
        bool $userIsMember = true,
        bool $withAuthenticatedUser = true,
        bool $withAuthorizedUser = true,
        bool $withOwner = false,
        bool $memberIsOwner = false
    ): array {
        if ($withOwner) {
            $role = $this->createOwnerRole();
        } elseif ($withAuthorizedUser) {
            $role = $this->createRoleWithPermission($this->getConcretePermission(PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT));
        } else {
            $role = $this->createRoleEntities()->first();
        }
        $user = $this->createUserWithRole($role);
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        $memberUser = $this->createUserEntities()->first();
        $memberRole = $this->createRoleEntities(1, [
            RoleModel::PROPERTY_PROJECT => $role->getProject(),
            RoleModel::PROPERTY_OWNER   => $memberIsOwner,
        ])->first();
        if ($userIsMember) {
            $member = $this->createMemberEntities(1, [
                MemberModel::PROPERTY_ROLE => $memberRole,
                MemberModel::PROPERTY_USER => $memberUser,
            ])->first();
            $memberRole->addMember($member);
            $memberUser->addMember($member);
        }

        $newRole = $this->createRoleEntities(
            1,
            [RoleModel::PROPERTY_PROJECT => $roleInProject ? $role->getProject() : $this->createProjectEntities()->first()]
        )->first();

        return [$role->getProject(), $memberUser, $newRole];
    }

    public function testChangeRole(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest();

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
                'role' => $role->getUuid(),
            ]
        );

        $response->assertNoContent();
        $this->assertEquals($role, $user->getMembers()->first()->getRole());
    }

    public function testChangeRoleWithProjectNotFound(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest();

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $this->getFaker()->uuid]),
            [
                'user' => $user->getUuid(),
                'role' => $role->getUuid(),
            ]
        );

        $response->assertNotFound();
    }

    public function testChangeRoleWithoutUser(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest();

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'role' => $role->getUuid(),
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['user' => ['validation.required']]);
    }

    public function testChangeRoleWithUserNotFound(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest();

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $this->getFaker()->uuid,
                'role' => $role->getUuid(),
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['user' => ['validation.user-not-found']]);
    }

    public function testChangeRoleWithoutRole(): void
    {
        [$project, $user] = $this->setUpChangeRoleTest();

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['role' => ['validation.required']]);
    }

    public function testChangeRoleWithRoleNotFound(): void
    {
        [$project, $user] = $this->setUpChangeRoleTest();

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
                'role' => $this->getFaker()->uuid,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['role' => ['validation.role-not-found']]);
    }

    public function testChangeRoleWithRoleNotInProject(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest(roleInProject: false);

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
                'role' => $role->getUuid(),
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['role' => ['validation.role-not-found']]);
    }

    public function testChangeRoleWithUserIsNotMember(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest(userIsMember: false);

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
                'role' => $role->getUuid(),
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['user' => ['validation.user-not-found']]);
    }

    public function testChangeRoleWithoutAuthenticatedUser(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest(withAuthenticatedUser: false);

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
                'role' => $role->getUuid(),
            ]
        );

        $response->assertStatus(401);
    }

    public function testChangeRoleWithoutAuthorizedUser(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest(withAuthorizedUser: false);

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
                'role' => $role->getUuid(),
            ]
        );

        $response->assertStatus(403);
    }

    public function testChangeRoleWithOwnerRole(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest(withOwner: true);

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
                'role' => $role->getUuid(),
            ]
        );

        $response->assertNoContent();
    }

    public function testChangeRoleWithMemberIsOwner(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest(memberIsOwner: true);

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
                'role' => $role->getUuid(),
            ]
        );

        $response->assertStatus(403);
    }

    public function testChangeRoleWithUserAndMemberAreOwner(): void
    {
        [$project, $user, $role] = $this->setUpChangeRoleTest(withOwner: true, memberIsOwner: true);

        $response = $this->doApiCall(
            'PUT',
            $this->getUrl(ProjectsController::ROUTE_NAME_CHANGE_ROLE, ['project' => $project->getUuid()]),
            [
                'user' => $user->getUuid(),
                'role' => $role->getUuid(),
            ]
        );

        $response->assertNoContent();
    }
}
