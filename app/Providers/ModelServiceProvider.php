<?php

namespace App\Providers;

use App\Models\DatabaseHandler;
use App\Models\DoctrineDatabaseHandler;
use App\Models\LaravelPasswordHasher;
use App\Models\PasswordHasher;
use App\Models\RamseyUuidGenerator;
use App\Models\UuidGenerator;
use App\Projects\Invites\InvitationDoctrineModel;
use App\Projects\Invites\InvitationDoctrineModelFactory;
use App\Projects\Invites\InvitationDoctrineRepository;
use App\Projects\Invites\InvitationModel;
use App\Projects\Invites\InvitationModelFactory;
use App\Projects\Invites\InvitationRepository;
use App\Projects\MemberDoctrineModel;
use App\Projects\MemberDoctrineModelFactory;
use App\Projects\MemberDoctrineRepository;
use App\Projects\MemberModel;
use App\Projects\MemberModelFactory;
use App\Projects\MemberRepository;
use App\Projects\MetaDataElementDoctrineModel;
use App\Projects\MetaDataElementDoctrineModelFactory;
use App\Projects\MetaDataElementDoctrineRepository;
use App\Projects\MetaDataElementModel;
use App\Projects\MetaDataElementModelFactory;
use App\Projects\MetaDataElementRepository;
use App\Projects\PermissionDoctrineModel;
use App\Projects\PermissionDoctrineRepository;
use App\Projects\PermissionModel;
use App\Projects\PermissionRepository;
use App\Projects\ProjectDoctrineModel;
use App\Projects\ProjectDoctrineModelFactory;
use App\Projects\ProjectDoctrineRepository;
use App\Projects\ProjectModel;
use App\Projects\ProjectModelFactory;
use App\Projects\ProjectRepository;
use App\Projects\RoleDoctrineModel;
use App\Projects\RoleDoctrineModelFactory;
use App\Projects\RoleDoctrineRepository;
use App\Projects\RoleModel;
use App\Projects\RoleModelFactory;
use App\Projects\RoleRepository;
use App\Users\UserDoctrineModel;
use App\Users\UserDoctrineModelFactory;
use App\Users\UserDoctrineRepository;
use App\Users\UserModel;
use App\Users\UserModelFactory;
use App\Users\UserRepository;
use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\ServiceProvider;
use Ramsey\Uuid\UuidFactory;

/**
 * Class ModelServiceProvider
 *
 * @package App\Providers
 */
final class ModelServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this
            ->bindModels()
            ->bindModelFactories()
            ->bindDatabaseHandler()
            ->bindRepositories()
            ->bindUuidGenerator()
            ->bindPasswordHasher();
    }

    /**
     * @return $this
     */
    private function bindModels(): self
    {
        $this->app->bind(UserModel::class, UserDoctrineModel::class);
        $this->app->bind(ProjectModel::class, ProjectDoctrineModel::class);
        $this->app->bind(MetaDataElementModel::class, MetaDataElementDoctrineModel::class);
        $this->app->bind(RoleModel::class, RoleDoctrineModel::class);
        $this->app->bind(MemberModel::class, MemberDoctrineModel::class);
        $this->app->bind(PermissionModel::class, PermissionDoctrineModel::class);
        $this->app->bind(InvitationModel::class, InvitationDoctrineModel::class);

        return $this;
    }

    /**
     * @return $this
     */
    private function bindModelFactories(): self
    {
        $this->app->singleton(UserModelFactory::class, UserDoctrineModelFactory::class);
        $this->app->singleton(ProjectModelFactory::class, ProjectDoctrineModelFactory::class);
        $this->app->singleton(MetaDataElementModelFactory::class, MetaDataElementDoctrineModelFactory::class);
        $this->app->singleton(RoleModelFactory::class, RoleDoctrineModelFactory::class);
        $this->app->singleton(MemberModelFactory::class, MemberDoctrineModelFactory::class);
        $this->app->singleton(InvitationModelFactory::class, fn () => new InvitationDoctrineModelFactory(
            $this->app->get(UuidGenerator::class),
            $this->app['config']['projects.invitations.validUntilMinutes']
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function bindDatabaseHandler(): self
    {
        $this->app->bind(
            DatabaseHandler::class,
            fn (Container $app, array $parameters) => new DoctrineDatabaseHandler($parameters[0], $parameters[1])
        );

        return $this;
    }

    /**
     * @param string $className
     *
     * @return DatabaseHandler
     */
    private function makeDatabaseHandler(string $className): DatabaseHandler
    {
        return $this->app->make(DatabaseHandler::class, [$this->app->get(EntityManager::class), $className]);
    }

    /**
     * @return $this
     */
    private function bindRepositories(): self
    {
        $this->app->singleton(
            UserRepository::class,
            fn () => new UserDoctrineRepository($this->makeDatabaseHandler(UserDoctrineModel::class))
        );
        $this->app->singleton(
            ProjectRepository::class,
            fn () => new ProjectDoctrineRepository($this->makeDatabaseHandler(ProjectDoctrineModel::class))
        );
        $this->app->singleton(
            MetaDataElementRepository::class,
            fn () => new MetaDataElementDoctrineRepository($this->makeDatabaseHandler(MetaDataElementDoctrineModel::class))
        );
        $this->app->singleton(
            RoleRepository::class,
            fn () => new RoleDoctrineRepository($this->makeDatabaseHandler(RoleDoctrineModel::class))
        );
        $this->app->singleton(
            MemberRepository::class,
            fn () => new MemberDoctrineRepository($this->makeDatabaseHandler(MemberDoctrineModel::class))
        );
        $this->app->singleton(
            PermissionRepository::class,
            fn () => new PermissionDoctrineRepository($this->makeDatabaseHandler(PermissionDoctrineModel::class))
        );
        $this->app->singleton(
            InvitationRepository::class,
            fn () => new InvitationDoctrineRepository($this->makeDatabaseHandler(InvitationDoctrineModel::class))
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function bindUuidGenerator(): self
    {
        $this->app->singleton(UuidGenerator::class, fn () => new RamseyUuidGenerator(new UuidFactory()));

        return $this;
    }

    private function bindPasswordHasher(): self
    {
        $this->app->singleton(
            PasswordHasher::class,
            fn () => new LaravelPasswordHasher($this->app->get(HashManager::class))
        );

        return $this;
    }
}
