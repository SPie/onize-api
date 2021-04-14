<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\ProjectsController;
use App\Projects\MetaDataModel;
use App\Projects\MetaDataRepository;
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
    public function testCreateProjectWithRoleAndMetaData(): void
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
            $user
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
        $metaData = $this->getMetaDataRepository()->findAll()->first();
        $this->assertEquals($metaDataName, $metaData->getName());
        $this->assertEquals($metaDataValue, $metaData->getValue());
        $this->assertEquals($project, $metaData->getProject());
        $this->assertEquals($user, $metaData->getUser());
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
        $user = $this->createUserEntities()->first();
        if ($withProjects) {
            $user->addRole($role);
        }
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
     * @param bool $withMetaDataOfUser
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
        bool $withMetaDataOfUser = true,
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
        $user = $this->createUserWithRole($role);
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }
        $project = $role->getProject();
        $metaData = $this->createMetaDataEntities(
            1,
            [
                MetaDataModel::PROPERTY_USER    => $withMetaDataOfUser ? $user : $this->createUserEntities()->first(),
                MetaDataModel::PROPERTY_PROJECT => $project,
            ]
        )->first();
        if ($withMembers) {
            $role->addUser($user);
        }
        if ($withRoles) {
            $project->addRole($role);
        }
        $project->setMetaData($withMetaData ? [$metaData] : []);
        $user->setMetaData($withMetaData ? [$metaData] : []);

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
         * @var MetaDataModel $metaData
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
                    UserModel::PROPERTY_UUID      => $user->getUuid(),
                    UserModel::PROPERTY_EMAIL     => $user->getEmail(),
                    UserModel::PROPERTY_META_DATA => [
                        $metaData->getName() => $metaData->getValue(),
                    ]
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
                   UserModel::PROPERTY_UUID      => $user->getUuid(),
                   UserModel::PROPERTY_EMAIL     => $user->getEmail(),
                   UserModel::PROPERTY_META_DATA => []
               ],
           ]
       ]);
    }

    /**
     * @return void
     */
    public function testMembersWithoutMetaDataOfUser(): void
    {
        /**
         * @var ProjectModel  $project
         * @var UserModel     $user
         */
        [$project, $user] = $this->setUpMembersTest(true, true, true, false);

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(ProjectsController::ROUTE_NAME_MEMBERS, ['project' => $project->getUuid()])
        );

        $response->assertOk();
        $response->assertJsonFragment([
           'members' => [
               [
                   UserModel::PROPERTY_UUID      => $user->getUuid(),
                   UserModel::PROPERTY_EMAIL     => $user->getEmail(),
                   UserModel::PROPERTY_META_DATA => []
               ],
           ]
       ]);
    }

    /**
     * @return void
     */
    public function testMembersWithoutProject(): void
    {
        $this->setUpMembersTest(true, true, true, true);

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
        [$project] = $this->setUpMembersTest(true, true, true, true, false);

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
        [$project] = $this->setUpMembersTest(true, true, true, true, true, false);

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

    /**
     * @return array
     */
    private function setUpInviteTest(): array
    {
        $role = $this->createRoleEntities()->first();
        $user = $this->createUserWithRole($role);
        $this->actingAs($user);
        $email = $this->getFaker()->safeEmail;
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
        $validUntil = $now->addDays(3);

        return [$email, $role, $metaData, $validUntil];
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
            $this->getUrl(ProjectsController::ROUTE_NAME_INVITE),
            [
                'email'    => $email,
                'role'     => $role->getUuid(),
                'metaData' => $metaData,
            ]
        );

        $response->assertCreated();
        // TODO check for created invitation and get uuid
        $response->assertJsonFragment([
            'invitation' => [
                'uuid'       => '', // TODO
                'email'      => $email,
                'role'       => $role->toArray(true),
                'metaData'   => $metaData,
                'validUntil' => $validUntil->format('Y-m-d H:i:s'),
                'acceptedAt' => null,
                'declinedAt' => null,
            ]
        ]);
    }

    //endregion

    /**
     * @return ProjectRepository
     */
    private function getConcreteProjectRepository(): ProjectRepository
    {
        return $this->app->get(ProjectRepository::class);
    }

    /**
     * @return RoleRepository
     */
    private function getConcreteRoleRepository(): RoleRepository
    {
        return $this->app->get(RoleRepository::class);
    }

    /**
     * @return MetaDataRepository
     */
    private function getMetaDataRepository(): MetaDataRepository
    {
        return $this->app->get(MetaDataRepository::class);
    }
}
