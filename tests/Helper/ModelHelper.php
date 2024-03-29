<?php

namespace Tests\Helper;

use App\Models\DatabaseHandler;
use App\Models\Model;
use App\Models\PasswordHasher;
use App\Models\Repository;
use App\Models\UuidGenerator;
use App\Models\UuidModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Mockery as m;
use Mockery\MockInterface;

trait ModelHelper
{
    /**
     * @return Model|MockInterface
     */
    private function createModel(): Model
    {
        return m::spy(Model::class);
    }

    private function mockModelGetId(MockInterface $model, ?int $id): self
    {
        $model
            ->shouldReceive('getId')
            ->andReturn($id);

        return $this;
    }

    /**
     * @return DatabaseHandler|MockInterface
     */
    private function createDatabaseHandler(): DatabaseHandler
    {
        return m::spy(DatabaseHandler::class);
    }

    private function mockDatabaseHandlerFind(MockInterface $databaseHandler, ?Model $model, int $id): self
    {
        $databaseHandler
            ->shouldReceive('find')
            ->with($id)
            ->andReturn($model);

        return $this;
    }

    private function mockDatabaseHandlerLoadAll(
        MockInterface $databaseHandler,
        Collection $models,
        array $criteria = null,
        array $orderBy = null,
        int $limit = null,
        int $offset = null
    ): self {
        $databaseHandler
            ->shouldReceive('loadAll')
            ->with($criteria, $orderBy, $limit, $offset)
            ->andReturn($models);

        return $this;
    }

    private function mockDatabaseHandlerLoad(MockInterface $databaseHandler, ?Model $model, array $criteria): self
    {
        $databaseHandler
            ->shouldReceive('load')
            ->with($criteria)
            ->andReturn($model);

        return $this;
    }

    private function mockDatabaseHandlerSave(MockInterface $databaseHandler, Model $model, bool $flush = null): self
    {
        $arguments = [$model];
        if ($flush !== null) {
            $arguments[] = $flush;
        }

        $databaseHandler
            ->shouldReceive('save')
            ->withArgs($arguments)
            ->andReturn($model);

        return $this;
    }

    private function assertDatabaseHandlerSave(MockInterface $databaseHandler, Model $model, bool $flush = null): self
    {
        $arguments = [$model];
        if ($flush !== null) {
            $arguments[] = $flush;
        }

        $databaseHandler
            ->shouldHaveReceived('save')
            ->withArgs($arguments)
            ->once();

        return $this;
    }

    private function assertDatabaseHandlerDelete(MockInterface $databaseHandler, Model $model, bool $flush): self
    {
        $databaseHandler
            ->shouldHaveReceived('delete')
            ->with($model, $flush)
            ->once();

        return $this;
    }

    private function assertDatabaseHandlerFlush(MockInterface $databaseHandler): self
    {
        $databaseHandler
            ->shouldHaveReceived('flush')
            ->once();

        return $this;
    }

    private function mockRepositorySave(MockInterface $repository, Model $model, bool $flush = null, Model $savedModel = null): self
    {
        $arguments = [$model];
        if ($flush !== null) {
            $arguments[] = $flush;
        }

        $repository
            ->shouldReceive('save')
            ->withArgs($arguments)
            ->andReturn($savedModel ?: $model)
            ->once();

        return $this;
    }

    private function assertRepositorySave(MockInterface $repository, Model $model, bool $flush = null): self
    {
        $arguments = [$model];
        if ($flush !== null) {
            $arguments[] = $flush;
        }

        $repository
            ->shouldHaveReceived('save')
            ->withArgs($arguments)
            ->once();

        return $this;
    }

    private function mockRepositoryFind(MockInterface $repository, ?Model $model, int $id): self
    {
        $repository
            ->shouldReceive('find')
            ->with($id)
            ->andReturn($model);

        return $this;
    }

    private function assertRepositoryFlush(MockInterface $repository): self
    {
        $repository->shouldHaveReceived('flush')->once();

        return $this;
    }

    private function assertRepositoryDelete(MockInterface $repository, Model $model): self
    {
        $repository
            ->shouldHaveReceived('delete')
            ->with($model)
            ->once();

        return $this;
    }

    /**
     * @return UuidGenerator|MockInterface
     */
    private function createUuidGenerator(string $uuid = null): UuidGenerator
    {
        $uuidGenerator = m::spy(UuidGenerator::class);
        $uuidGenerator
            ->shouldReceive('generate')
            ->andReturn($uuid ?: $this->getFaker()->uuid);

        return $uuidGenerator;
    }

    /**
     * @return PasswordHasher|MockInterface
     */
    private function createPasswordHasher(): PasswordHasher
    {
        return m::spy(PasswordHasher::class);
    }

    private function mockPasswordHasherHash(MockInterface $passwordHasher, string $hash, string $password): self
    {
        $passwordHasher
            ->shouldReceive('hash')
            ->with($password)
            ->andReturn($hash);

        return $this;
    }

    private function mockPasswordHasherCheck(
        MockInterface $passwordHasher,
        bool $valid,
        string $password,
        string $hashedPassword
    ): self {
        $passwordHasher
            ->shouldReceive('check')
            ->with($password, $hashedPassword)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @return Model[]|Collection
     */
    private function createModelEntities(string $className, int $times = 1, array $attributes = []): Collection
    {
        if ($times = 1) {
            return new ArrayCollection([entity($className, 1)->create($attributes)]);
        }

        return entity($className, $times)->create($attributes);
    }

    private function mockUuidModelGetUuid(MockInterface $uuidModel, string $uuid): self
    {
        $uuidModel
            ->shouldReceive('getUuid')
            ->andReturn($uuid);

        return $this;
    }

    /**
     * @return LifecycleEventArgs|MockInterface
     */
    private function createLifecycleEventArgs(): LifecycleEventArgs
    {
        return m::spy(LifecycleEventArgs::class);
    }

    private function mockLifecycleEventArgsGetEntity(MockInterface $lifecycleVentArgs, $entity): self
    {
        $lifecycleVentArgs
            ->shouldReceive('getEntity')
            ->andReturn($entity);

        return $this;
    }
}
