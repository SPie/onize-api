<?php

namespace Tests\Unit\Models;

use App\Models\UuidExtension;
use App\Models\UuidGenerator;
use App\Models\UuidModel;
use Tests\Helper\ModelHelper;
use Tests\TestCase;

final class UuidExtensionTest extends TestCase
{
    use ModelHelper;

    //region Tests

    public function testPrePersist(): void
    {
        $model = new class () implements UuidModel {
            public ?string $uuid = null;

            public function setUuid(string $uuid): UuidModel
            {
                $this->uuid = $uuid;

                return $this;
            }

            public function getUuid(): ?string
            {
                return $this->uuid;
            }
        };
        $lifecycleEventArgs = $this->createLifecycleEventArgs();
        $this->mockLifecycleEventArgsGetEntity($lifecycleEventArgs, $model);
        $uuid = $this->getFaker()->uuid;
        $uuidGenerator = $this->createUuidGenerator($uuid);

        $this->getUuidExtension($uuidGenerator)->prePersist($lifecycleEventArgs);

        $this->assertEquals($uuid, $model->uuid);
    }

    public function testPrePersistWithoutUuidModel(): void
    {
        $model = new class () {
            public ?string $uuid = null;
        };
        $lifecycleEventArgs = $this->createLifecycleEventArgs();
        $this->mockLifecycleEventArgsGetEntity($lifecycleEventArgs, $model);
        $uuid = $this->getFaker()->uuid;
        $uuidGenerator = $this->createUuidGenerator($uuid);

        $this->getUuidExtension($uuidGenerator)->prePersist($lifecycleEventArgs);

        $this->assertNull($model->uuid);
    }

    public function testPrePersistWithModelWithUuid(): void
    {
        $model = new class () implements UuidModel {
            public ?string $uuid = null;

            public function setUuid(string $uuid): UuidModel
            {
                $this->uuid = $uuid;

                return $this;
            }

            public function getUuid(): ?string
            {
                return $this->uuid;
            }
        };
        $uuid = $this->getFaker()->uuid;
        $model->setUuid($uuid);
        $lifecycleEventArgs = $this->createLifecycleEventArgs();
        $this->mockLifecycleEventArgsGetEntity($lifecycleEventArgs, $model);
        $newUuid = $this->getFaker()->uuid;
        $uuidGenerator = $this->createUuidGenerator($newUuid);

        $this->getUuidExtension($uuidGenerator)->prePersist($lifecycleEventArgs);

        $this->assertEquals($uuid, $model->uuid);
    }

    //endregion

    private function getUuidExtension(UuidGenerator $uuidGenerator = null): UuidExtension
    {
        return new UuidExtension($uuidGenerator ?: $this->createUuidGenerator());
    }
}
