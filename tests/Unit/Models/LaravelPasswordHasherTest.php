<?php

namespace Tests\Unit\Models;

use App\Models\LaravelPasswordHasher;
use Illuminate\Hashing\HashManager;
use Mockery as m;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class LaravelPasswordHasherTest
 *
 * @package Tests\Unit\Models
 */
final class LaravelPasswordHasherTest extends TestCase
{
    //region Tests

    /**
     * @return void
     */
    public function testHash(): void
    {
        $password = $this->getFaker()->password;
        $hash = $this->getFaker()->sha256;
        $hashManager = $this->createHashManager();
        $this->mockHashManagerMake($hashManager, $hash, $password);

        $this->assertEquals($hash, $this->getLaravelPasswordHasher($hashManager)->hash($password));
    }

    //endregion

    /**
     * @param HashManager|null $hashManager
     *
     * @return LaravelPasswordHasher
     */
    private function getLaravelPasswordHasher(HashManager $hashManager = null): LaravelPasswordHasher
    {
        return new LaravelPasswordHasher($hashManager ?: $this->createHashManager());
    }

    /**
     * @return HashManager|MockInterface
     */
    private function createHashManager(): HashManager
    {
        return m::spy(HashManager::class);
    }

    /**
     * @param HashManager|MockInterface $hashManager
     * @param string                    $hash
     * @param string                    $password
     *
     * @return $this
     */
    private function mockHashManagerMake(MockInterface $hashManager, string $hash, string $password): self
    {
        $hashManager
            ->shouldReceive('make')
            ->with($password)
            ->andReturn($hash);

        return $this;
    }
}
