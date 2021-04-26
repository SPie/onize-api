<?php

namespace Tests\Unit\Projects\MetaData;

use App\Projects\MetaData\LaravelAttributesValidator;
use App\Projects\MetaData\MetaDataLaravelValidator;
use Illuminate\Validation\Validator;
use Mockery as m;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class MetaDataLaravelValidatorTest
 *
 * @package Tests\Unit\Projects\MetaData
 */
final class MetaDataLaravelValidatorTest extends TestCase
{
    //region Tests

    /**
     * @return array
     */
    private function setUpIsValidStringTest(bool $valid = true): array
    {
        $value = $this->getFaker()->word;
        $validator = $this->createValidator();
        $this->mockValidatorValidateString($validator, $valid, '', $value);
        $metaDataValidator = $this->getMetaDataLaravelValidator($validator);

        return [$metaDataValidator, $value];
    }

    /**
     * @return void
     */
    public function testIsValidString(): void
    {
        /** @var MetaDataLaravelValidator $metaDataValidator */
        [$metaDataValidator, $value] = $this->setUpIsValidStringTest();

        $this->assertTrue($metaDataValidator->isValidString($value));
    }

    /**
     * @return void
     */
    public function testIsValidStringWithoutString(): void
    {
        /** @var MetaDataLaravelValidator $metaDataValidator */
        [$metaDataValidator, $value] = $this->setUpIsValidStringTest(false);

        $this->assertFalse($metaDataValidator->isValidString($value));
    }

    /**
     * @param bool $valid
     *
     * @return array
     */
    private function setUpIsValidEmailTest(bool $valid = true): array
    {
        $value = $this->getFaker()->safeEmail;
        $validator = $this->createValidator();
        $this->mockValidatorValidateEmail($validator, $valid, '', $value, []);
        $metaDataValidator = $this->getMetaDataLaravelValidator($validator);

        return [$metaDataValidator, $value];
    }

    /**
     * @return void
     */
    public function testIsValidEmail(): void
    {
        /** @var MetaDataLaravelValidator $metaDataValidator */
        [$metaDataValidator, $value] = $this->setUpIsValidEmailTest();

        $this->assertTrue($metaDataValidator->isValidEmail($value));
    }

    /**
     * @return void
     */
    public function testIsValidEmailWithoutEmail(): void
    {
        /** @var MetaDataLaravelValidator $metaDataValidator */
        [$metaDataValidator, $value] = $this->setUpIsValidEmailTest(false);

        $this->assertFalse($metaDataValidator->isValidEmail($value));
    }

    /**
     * @return array
     */
    private function setUpIsValidNumericTest(bool $valid = true): array
    {
        $value = $this->getFaker()->safeEmail;
        $validator = $this->createValidator();
        $this->mockValidatorValidateNumeric($validator, $valid, '', $value);
        $metaDataValidator = $this->getMetaDataLaravelValidator($validator);

        return [$metaDataValidator, $value];
    }

    /**
     * @return void
     */
    public function testIsValidNumeric(): void
    {
        /** @var MetaDataLaravelValidator $metaDataValidator */
        [$metaDataValidator, $value] = $this->setUpIsValidNumericTest();

        $this->assertTrue($metaDataValidator->isValidNumeric($value));
    }

    /**
     * @return void
     */
    public function testIsValidNumericWithoutNumeric(): void
    {
        /** @var MetaDataLaravelValidator $metaDataValidator */
        [$metaDataValidator, $value] = $this->setUpIsValidNumericTest(false);

        $this->assertFalse($metaDataValidator->isValidNumeric($value));
    }

    /**
     * @param bool $valid
     *
     * @return array
     */
    private function setUpIsValidDateTimeTest(bool $valid = true): array
    {
        $value = $this->getFaker()->safeEmail;
        $validator = $this->createValidator();
        $this->mockValidatorValidateDate($validator, $valid, '', $value);
        $metaDataValidator = $this->getMetaDataLaravelValidator($validator);

        return [$metaDataValidator, $value];
    }

    /**
     * @return void
     */
    public function testIsValidDateTime(): void
    {
        /** @var MetaDataLaravelValidator $metaDataValidator */
        [$metaDataValidator, $value] = $this->setUpIsValidDateTimeTest();

        $this->assertTrue($metaDataValidator->isValidDateTime($value));
    }

    /**
     * @return void
     */
    public function testIsValidDateTimeWithoutDateTime(): void
    {
        /** @var MetaDataLaravelValidator $metaDataValidator */
        [$metaDataValidator, $value] = $this->setUpIsValidDateTimeTest(false);

        $this->assertFalse($metaDataValidator->isValidDateTime($value));
    }

    //endregion

    /**
     * @param LaravelAttributesValidator|null $validator
     *
     * @return MetaDataLaravelValidator
     */
    private function getMetaDataLaravelValidator(LaravelAttributesValidator $validator = null): MetaDataLaravelValidator
    {
        return new MetaDataLaravelValidator($validator ?: $this->createValidator());
    }

    /**
     * @return LaravelAttributesValidator|MockInterface
     */
    private function createValidator(): LaravelAttributesValidator
    {
        return m::spy(LaravelAttributesValidator::class);
    }

    /**
     * @param LaravelAttributesValidator|MockInterface $validator
     * @param bool                                     $valid
     * @param string                                   $attribute
     * @param mixed                                    $value
     *
     * @return $this
     */
    private function mockValidatorValidateString(MockInterface $validator, bool $valid, string $attribute, $value): self
    {
        $validator
            ->shouldReceive('validateString')
            ->with($attribute, $value)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param LaravelAttributesValidator|MockInterface $validator
     * @param bool                                     $valid
     * @param string                                   $attribute
     * @param mixed                                    $value
     * @param array                                    $parameters
     *
     * @return $this
     */
    private function mockValidatorValidateEmail(
        MockInterface $validator,
        bool $valid,
        string $attribute,
        $value,
        array $parameters
    ): self {
        $validator
            ->shouldReceive('validateEmail')
            ->with($attribute, $value, $parameters)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param LaravelAttributesValidator|MockInterface $validator
     * @param bool                                     $valid
     * @param string                                   $attribute
     * @param mixed                                    $value
     *
     * @return $this
     */
    private function mockValidatorValidateNumeric(MockInterface $validator, bool $valid, string $attribute, $value): self
    {
        $validator
            ->shouldReceive('validateNumeric')
            ->with($attribute, $value)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param LaravelAttributesValidator|MockInterface $validator
     * @param bool                                     $valid
     * @param string                                   $attribute
     * @param mixed                                    $value
     *
     * @return $this
     */
    private function mockValidatorValidateDate(MockInterface $validator, bool $valid, string $attribute, $value): self
    {
        $validator
            ->shouldReceive('validateDate')
            ->with($attribute, $value)
            ->andReturn($valid);

        return $this;
    }
}
