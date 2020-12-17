<?php

namespace Tests\Unit\Http\Requests\Projects;

use App\Http\Requests\Projects\Create;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Validation\Validator;
use Mockery\MockInterface;
use Tests\Helper\HttpHelper;
use Tests\Helper\ReflectionHelper;
use Tests\TestCase;

/**
 * Class CreateTest
 *
 * @package Tests\Unit\Http\Requests\Projects
 */
final class CreateTest extends TestCase
{
    use HttpHelper;
    use ReflectionHelper;

    //region Tests

    /**
     * @return void
     */
    public function testRules(): void
    {
        $this->assertEquals(
            [
                'label'                       => ['required', 'string'],
                'description'                 => ['required', 'string'],
                'metaDataElements'            => ['present', 'array'],
                'metaDataElements.*.name'     => ['required', 'string'],
                'metaDataElements.*.label'    => ['required', 'string'],
                'metaDataElements.*.required' => ['boolean'],
                'metaDataElements.*.inList'   => ['boolean'],
                'metaDataElements.*.type'     => [
                    'required',
                    \sprintf('in:%s', \implode(',', ['email', 'string', 'date', 'numeric'])),
                ],
                'metaData'                    => ['present', function () {
                }],
            ],
            $this->getCreate()->rules()
        );
    }

    /**
     * @return void
     */
    public function testGetLabel(): void
    {
        $label = $this->getFaker()->word;
        $request = $this->getCreate();
        $request->offsetSet('label', $label);

        $this->assertEquals($label, $request->getLabel());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $description = $this->getFaker()->word;
        $request = $this->getCreate();
        $request->offsetSet('description', $description);

        $this->assertEquals($description, $request->getDescription());
    }

    /**
     * @return void
     */
    public function testGetMetaDataElements(): void
    {
        $metaDataElements = [$this->getFaker()->word => $this->getFaker()->word];
        $request = $this->getCreate();
        $request->offsetSet('metaDataElements', $metaDataElements);

        $this->assertEquals($metaDataElements, $request->getMetaDataElements());
    }

    /**
     * @return void
     */
    public function testGetMetaDataElementsWithoutElements(): void
    {
        $this->assertEquals([], $this->getCreate()->getMetaDataElements());
    }

    /**
     * @return void
     */
    public function testGetMetaData(): void
    {
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $request = $this->getCreate();
        $request->offsetSet('metaData', $metaData);

        $this->assertEquals($metaData, $request->getMetaData());
    }

    /**
     * @return void
     */
    public function testGetMetaDataWithoutMetaData(): void
    {
        $this->assertEquals([], $this->getCreate()->getMetaData());
    }

    /**
     * @param bool $withOptionalMetaData
     * @param bool $withRequiredMetaData
     * @param bool $withValidMetaData
     *
     * @return array
     */
    private function setUpTestValidateMetaDataRuleTest(
        bool $withOptionalMetaData = true,
        bool $withRequiredMetaData = true,
        bool $withValidMetaData = true
    ): array {
        $metaDataElements = [
            [
                'name' => 'username',
                'required' => false,
                'type' => 'string',
            ],
            [
                'name' => 'email',
                'required' => true,
                'type' => 'email',
            ],
            [
                'name' => 'count',
                'required' => false,
                'type' => 'numeric',
            ],
            [
                'name' => 'birthday',
                'required' => false,
                'type' => 'date',
            ],
            [
                'name' => 'test',
                'type' => 'string',
            ]
        ];
        $messageBag = $this->createMessageBag();
        $validator = $this->createValidator();
        $metaData = [];
        if ($withRequiredMetaData) {
            $metaData['email'] = $withValidMetaData ? $this->getFaker()->safeEmail : $this->getFaker()->word;
            $this->mockValidatorValidateEmail($validator, $withValidMetaData, 'metaData', $metaData['email'], []);
        }
        if ($withOptionalMetaData) {
            $metaData['username'] = $withValidMetaData ? $this->getFaker()->word : $this->getFaker()->numberBetween();
            $metaData['count'] = $withValidMetaData ? $this->getFaker()->numberBetween() : $this->getFaker()->word;
            $metaData['birthday'] = $withValidMetaData ? $this->getFaker()->dateTime->format('Y-m-d H:i:s') : $this->getFaker()->word;

            $this->mockValidatorValidateString($validator, $withValidMetaData, 'metaData', $metaData['username']);
            $this->mockValidatorValidateNumeric($validator, $withValidMetaData, 'metaData', $metaData['count']);
            $this->mockValidatorValidateDate($validator, $withValidMetaData, 'metaData', $metaData['birthday']);
        }
        $this->mockValidatorGetMessageBag($validator, $messageBag);
        $request = $this->getCreate();
        $request->offsetSet('metaDataElements', $metaDataElements);
        $request->setValidator($validator);
        $rule = $this->runPrivateMethod($request, 'getValidateMetaDataRule');

        return [$rule, $metaDataElements, $metaData, $messageBag];
    }

    /**
     * @return void
     */
    public function testValidateMetaDataRule(): void
    {
        [$rule, $metaDataElements, $metaData] = $this->setUpTestValidateMetaDataRuleTest();

        $this->assertTrue($rule('metaData', $metaData, function () {
        }));
    }

    /**
     * @return void
     */
    public function testValidateMetaDataRuleWithoutMetaDataArray(): void
    {
        [$rule] = $this->setUpTestValidateMetaDataRuleTest();
        $fail = function ($message) use (&$validationError) {
            $validationError = $message;
        };

        $this->assertFalse($rule('metaData', $this->getFaker()->word, $fail));
        $this->assertEquals('validation.array', $validationError);
    }

    /**
     * @return void
     */
    public function testValidateMetaDataRuleWithoutNonRequiredMetadataField(): void
    {
        [$rule, $metaDataElements, $metaData] = $this->setUpTestValidateMetaDataRuleTest(false);

        $this->assertTrue($rule('metaData', $metaData, function () {
        }));
    }

    /**
     * @return void
     */
    public function testValidateMetaDataRuleWithoutRequiredMetadataField(): void
    {
        /** @var MessageBag|MockInterface $messageBag */
        [$rule, $metaDataElements, $metaData, $messageBag] = $this->setUpTestValidateMetaDataRuleTest(true, false);

        $this->assertFalse($rule('metaData', [], function () {
        }));
        $messageBag
            ->shouldHaveReceived('merge')
            ->with(['metaData' => [$metaDataElements[1]['name'] => [\sprintf('validation.required')]]])
            ->once();
    }

    /**
     * @return void
     */
    public function testValidateMetaDataRuleWithoutExistingMetaDataElement(): void
    {
        /** @var MessageBag|MockInterface $messageBag */
        [$rule, $metaDataElements, $metaData, $messageBag] = $this->setUpTestValidateMetaDataRuleTest();
        $metaData = \array_merge($metaData, ['phone' => $this->getFaker()->phoneNumber]);

        $this->assertFalse($rule('metaData', $metaData, function () {
        }));
        $messageBag
            ->shouldHaveReceived('merge')
            ->with(['metaData' => ['phone' => [\sprintf('validation.not-existing')]]])
            ->once();
    }

    /**
     * @return void
     */
    public function testValidateMetaDataRuleWithInvalidMetaData(): void
    {
        /** @var MessageBag|MockInterface $messageBag */
        [$rule, $metaDataElements, $metaData, $messageBag] = $this->setUpTestValidateMetaDataRuleTest(
            true,
            true,
            false
        );

        $this->assertFalse($rule('metaData', $metaData, function () {
        }));
        $messageBag
            ->shouldHaveReceived('merge')
            ->with(['metaData' => [
                'email'    => ['validation.email'],
                'username' => ['validation.string'],
                'count'    => ['validation.numeric'],
                'birthday' => ['validation.date'],
            ]])
            ->once();
    }

    //endregion

    /**
     * @return Create
     */
    private function getCreate(): Create
    {
        return new Create();
    }
}
