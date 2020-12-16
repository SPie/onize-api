<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\ProjectsController;
use App\Projects\MetaDataElementModel;
use App\Projects\MetaDataRepository;
use App\Projects\ProjectModel;
use App\Projects\ProjectRepository;
use App\Projects\RoleRepository;
use App\Users\UserModel;
use LaravelDoctrine\Migrations\Testing\DatabaseMigrations;
use Tests\Feature\FeatureTestCase;
use Tests\Helper\ApiHelper;
use Tests\Helper\ModelHelper;
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