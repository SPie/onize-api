<?php

namespace Tests\Unit\Projects\MetaData;

use App\Projects\MetaData\MetaDataManager;
use App\Projects\MetaData\MetaDataValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class MetaDataManagerTest
 *
 * @package Tests\Unit\Projects
 */
final class MetaDataManagerTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return array
     */
    private function setUpValidateMetaDataTest(
        bool $metaDataExists = true,
        bool $required = false,
        string $type = 'string',
        bool $validType = true
    ): array {
        $metaDataName = $this->getFaker()->word;
        $value = $this->getFaker()->word;
        $metaDataElement = $this->createMetaDataElementModel();
        $this
            ->mockMetaDataElementModelGetName($metaDataElement, $metaDataName . ($metaDataExists ? '' : $this->getFaker()->word))
            ->mockMetaDataElementModelIsRequired($metaDataElement, $required)
            ->mockMetaDataElementModelGetType($metaDataElement, $type);
        $project = $this->createProjectModel();
        $this->mockProjectModelGetMetaDataElements($project, new ArrayCollection([$metaDataElement]));
        $metaData = $required ? [] : [$metaDataName => $value];
        $metaDataValidator = $this->createMetaDataValidator();
        $this
            ->mockMetaDataValidatorIsValidString($metaDataValidator, $validType, $value)
            ->mockMetaDataValidatorIsValidEmail($metaDataValidator, $validType, $value)
            ->mockMetaDataValidatorIsValidNumeric($metaDataValidator, $validType, $value)
            ->mockMetaDataValidatorIsValidDateTime($metaDataValidator, $validType, $value);
        $metaDataManager = $this->getMetaDataManager($metaDataValidator);

        return [$metaDataManager, $project, $metaData, $metaDataName];
    }

    /**
     * @return void
     */
    public function testValidateMetaDataWithValidMetaData(): void
    {
        /** @var MetaDataManager $metaDataManager */
        [$metaDataManager, $project, $metaData] = $this->setUpValidateMetaDataTest();

        $this->assertEmpty($metaDataManager->validateMetaData($project, $metaData));
    }

    /**
     * @return void
     */
    public function testValidateMetaDataWithNonExistingMetaData(): void
    {
        /** @var MetaDataManager $metaDataManager */
        [$metaDataManager, $project, $metaData, $metaDataName] = $this->setUpValidateMetaDataTest(false);

        $this->assertEquals(
            [$metaDataName => ['not-existing']],
            $metaDataManager->validateMetaData($project, $metaData)
        );
    }

    /**
     * @return void
     */
    public function testValidateMetaDataWithoutRequiredMetaData(): void
    {
        /** @var MetaDataManager $metaDataManager */
        [$metaDataManager, $project, $metaData, $metaDataName] = $this->setUpValidateMetaDataTest(true, true);

        $this->assertEquals(
            [$metaDataName => ['required']],
            $metaDataManager->validateMetaData($project, $metaData)
        );
    }

    /**
     * @return void
     */
    public function testValidateMetaDataWithInvalidStringMetaData(): void
    {
        /** @var MetaDataManager $metaDataManager */
        [$metaDataManager, $project, $metaData, $metaDataName] = $this->setUpValidateMetaDataTest(
            true,
            false,
            'string',
            false
        );

        $this->assertEquals(
            [$metaDataName => ['string']],
            $metaDataManager->validateMetaData($project, $metaData)
        );
    }

    /**
     * @return void
     */
    public function testValidateMetaDataWithInvalidEmailMetaData(): void
    {
        /** @var MetaDataManager $metaDataManager */
        [$metaDataManager, $project, $metaData, $metaDataName] = $this->setUpValidateMetaDataTest(
            true,
            false,
            'email',
            false
        );

        $this->assertEquals(
            [$metaDataName => ['email']],
            $metaDataManager->validateMetaData($project, $metaData)
        );
    }

    /**
     * @return void
     */
    public function testValidateMetaDataWithInvalidNumericMetaData(): void
    {
        /** @var MetaDataManager $metaDataManager */
        [$metaDataManager, $project, $metaData, $metaDataName] = $this->setUpValidateMetaDataTest(
            true,
            false,
            'numeric',
            false
        );

        $this->assertEquals(
            [$metaDataName => ['numeric']],
            $metaDataManager->validateMetaData($project, $metaData)
        );
    }

    /**
     * @return void
     */
    public function testValidateMetaDataWithInvalidDateTimeMetaData(): void
    {
        /** @var MetaDataManager $metaDataManager */
        [$metaDataManager, $project, $metaData, $metaDataName] = $this->setUpValidateMetaDataTest(
            true,
            false,
            'date',
            false
        );

        $this->assertEquals(
            [$metaDataName => ['date']],
            $metaDataManager->validateMetaData($project, $metaData)
        );
    }

    //endregion

    /**
     * @param MetaDataValidator|null $metaDataValidator
     *
     * @return MetaDataManager
     */
    private function getMetaDataManager(MetaDataValidator $metaDataValidator = null): MetaDataManager
    {
        return new MetaDataManager(
            $metaDataValidator ?: $this->createMetaDataValidator()
        );
    }
}
