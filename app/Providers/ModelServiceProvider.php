<?php

namespace App\Providers;

use App\Auth\RefreshTokenDoctrineModel;
use App\Auth\RefreshTokenDoctrineModelFactory;
use App\Auth\RefreshTokenDoctrineRepository;
use App\Auth\RefreshTokenModel;
use App\Auth\RefreshTokenModelFactory;
use App\Auth\RefreshTokenRepository;
use App\Models\DatabaseHandler;
use App\Models\DoctrineDatabaseHandler;
use App\Models\LaravelPasswordHasher;
use App\Models\PasswordHasher;
use App\Models\RamseyUuidGenerator;
use App\Models\UuidGenerator;
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
        $this->app->bind(RefreshTokenModel::class, RefreshTokenDoctrineModel::class);

        return $this;
    }

    /**
     * @return $this
     */
    private function bindModelFactories(): self
    {
        $this->app->singleton(UserModelFactory::class, UserDoctrineModelFactory::class);
        $this->app->singleton(RefreshTokenModelFactory::class, RefreshTokenDoctrineModelFactory::class);

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
            RefreshTokenRepository::class,
            fn () => new RefreshTokenDoctrineRepository($this->makeDatabaseHandler(RefreshTokenDoctrineModel::class))
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
