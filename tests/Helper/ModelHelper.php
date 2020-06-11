<?php

namespace Tests\Helper;

use App\Models\DatabaseHandler;
use App\Models\Model;
use App\Models\PasswordHasher;
use App\Models\Repository;
use App\Models\UuidGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mockery as m;
use Mockery\MockInterface;

/**
 * Trait ModelHelper
 *
 * @package Tests\Helper
 */
trait ModelHelper
{
    /**
     * @return Model
     */
    private function createModel(): Model
    {
        return m::spy(Model::class);
    }

    /**
     * @return DatabaseHandler|MockInterface
     */
    private function createDatabaseHandler(): DatabaseHandler
    {
        return m::spy(DatabaseHandler::class);
    }

    /**
     * @param DatabaseHandler|MockInterface $databaseHandler
     * @param Model|null                    $model
     * @param int                           $id
     *
     * @return $this
     */
    private function mockDatabaseHandlerFind(MockInterface $databaseHandler, ?Model $model, int $id): self
    {
        $databaseHandler
            ->shouldReceive('find')
            ->with($id)
            ->andReturn($model);

        return $this;
    }

    /**
     * @param DatabaseHandler|MockInterface $databaseHandler
     * @param Collection                    $models
     * @param array|null                    $criteria
     * @param array|null                    $orderBy
     * @param int|null                      $limit
     * @param int|null                      $offset
     *
     * @return $this
     */
    private function mockDatabaseHandlerLoadAll(
        MockInterface $databaseHandler,
        Collection $models,
        array $criteria = null,
        array $orderBy = null,
        int $limit = null,
        int $offset = null
    ): self {
        $arguments = [];
        if ($criteria !== null || $orderBy !== null || $limit !== null || $offset !== null) {
            $arguments[] = $criteria;
        }
        if ($orderBy !== null || $limit !== null || $offset !== null) {
            $arguments[] = $orderBy;
        }
        if ($limit !== null || $offset !== null) {
            $arguments[] = $limit;
        }
        if ($offset !== null) {
            $arguments[] = $offset;
        }

        $databaseHandler
            ->shouldReceive('loadAll')
            ->withArgs($arguments)
            ->andReturn($models);

        return $this;
    }

    /**
     * @param DatabaseHandler|MockInterface $databaseHandler
     * @param Model|null                    $model
     * @param array                         $criteria
     *
     * @return $this
     */
    private function mockDatabaseHandlerLoad(MockInterface $databaseHandler, ?Model $model, array $criteria): self
    {
        $databaseHandler
            ->shouldReceive('load')
            ->with($criteria)
            ->andReturn($model);

        return $this;
    }

    /**
     * @param DatabaseHandler|MockInterface $databaseHandler
     * @param Model                         $model
     * @param bool|null                     $flush
     *
     * @return $this
     */
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

    /**
     * @param DatabaseHandler|MockInterface $databaseHandler
     * @param Model                         $model
     * @param bool|null                     $flush
     *
     * @return $this
     */
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

    /**
     * @param DatabaseHandler|MockInterface $databaseHandler
     * @param Model                         $model
     * @param bool                          $flush
     *
     * @return $this
     */
    private function assertDatabaseHandlerDelete(MockInterface $databaseHandler, Model $model, bool $flush): self
    {
        $databaseHandler
            ->shouldHaveReceived('delete')
            ->with($model, $flush)
            ->once();

        return $this;
    }

    /**
     * @param DatabaseHandler|MockInterface $databaseHandler
     *
     * @return $this
     */
    private function assertDatabaseHandlerFlush(MockInterface $databaseHandler): self
    {
        $databaseHandler
            ->shouldHaveReceived('flush')
            ->once();

        return $this;
    }

    /**
     * @param Repository|MockInterface $repository
     * @param Model                    $model
     *
     * @return $this
     */
    private function mockRepositorySave(MockInterface $repository, Model $model): self
    {
        $repository
            ->shouldReceive('save')
            ->with($model)
            ->andReturn($model);

        return $this;
    }

    /**
     * @param Repository|MockInterface $repository
     * @param Model                    $model
     *
     * @return $this
     */
    private function assertRepositorySave(MockInterface $repository, Model $model): self
    {
        $repository
            ->shouldHaveReceived('save')
            ->with(m::on(fn (Model $actual) => $model == $actual))
            ->with($model)
            ->once();

        return $this;
    }

    /**
     * @param string|null $uuid
     *
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

    /**
     * @param PasswordHasher|MockInterface $passwordHasher
     * @param string                       $hash
     * @param string                       $password
     *
     * @return $this
     */
    private function mockPasswordHasherHash(MockInterface $passwordHasher, string $hash, string $password): self
    {
        $passwordHasher
            ->shouldReceive('hash')
            ->with($password)
            ->andReturn($hash);

        return $this;
    }

    /**
     * @param string $className
     * @param int    $times
     * @param array  $attributes
     *
     * @return Model[]|Collection
     */
    private function createModelEntities(string $className, int $times = 1, array $attributes = []): Collection
    {
        if ($times = 1) {
            return new ArrayCollection([entity($className, 1)->create($attributes)]);
        }

        return entity($className, $times)->create($attributes);
    }
}
