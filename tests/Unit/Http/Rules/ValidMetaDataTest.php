<?php

namespace Tests\Unit\Http\Rules;

use App\Http\Rules\ValidMetaData;
use App\Projects\MetaData\MetaDataManager;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class ValidMetaDataTest extends TestCase
{
    use ProjectHelper;

    private function setUpPassesTest(bool $validMetaData = true, bool $withMetaData = true, bool $withProject = true): array
    {
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $project = $this->createProjectModel();
        $metaDataName = $this->getFaker()->word;
        $metaDataErrorMessage = $this->getFaker()->word;
        $metaDataManager = $this->createMetaDataManager();
        $this->mockMetaDataManagerValidateMetaData(
            $metaDataManager,
            $validMetaData ? [] : [$metaDataName => [$metaDataErrorMessage]],
            $project,
            $withMetaData ? $metaData : []
        );
        $rule = $this->getValidMetaDataRule($metaDataManager);
        if ($withProject) {
            $rule->setProject($project);
        }

        return [$rule, $metaData, $metaDataName, $metaDataErrorMessage];
    }

    public function testPasses(): void
    {
        /** @var ValidMetaData $rule */
        [$rule, $metaData] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, $metaData));
    }

    public function testPassesWithoutArray(): void
    {
        /** @var ValidMetaData $rule */
        [$rule] = $this->setUpPassesTest();

        $this->assertFalse($rule->passes($this->getFaker()->word, $this->getFaker()->word));
        $this->assertEquals('validation.array', $rule->message());
    }

    public function testPassesWithValidationError(): void
    {
        /** @var ValidMetaData $rule */
        [$rule, $metaData, $metaDataName, $metaDataErrorMessage] = $this->setUpPassesTest(false);

        $this->assertFalse($rule->passes($this->getFaker()->word, $metaData));
        $this->assertEquals([\sprintf('%s.validation.%s', $metaDataName, $metaDataErrorMessage)], $rule->message());
    }

    public function testPassesWithoutMetaData(): void
    {
        /** @var ValidMetaData $rule */
        [$rule] = $this->setUpPassesTest(true, false);

        $this->assertTrue($rule->passes($this->getFaker()->word, null));
    }

    public function testPassesWithoutProject(): void
    {
        /** @var ValidMetaData $rule */
        [$rule, $metaData] = $this->setUpPassesTest(true, true, false);

        $this->assertTrue($rule->passes($this->getFaker()->word, $metaData));
    }

    private function getValidMetaDataRule(MetaDataManager $metaDataManager = null): ValidMetaData
    {
        return new ValidMetaData($metaDataManager ?: $this->createMetaDataManager());
    }
}
