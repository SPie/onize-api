<?php

namespace App\Http\Requests\Projects;

use App\Projects\MetaData\MetaDataManager;
use App\Projects\RoleModel;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Invite
 *
 * @package App\Http\Requests\Projects
 */
class Invite extends FormRequest
{
    private const PARAMETER_ROLE = 'role';
    private const PARAMETER_EMAIL = 'email';
    private const PARAMETER_META_DATA = 'metaData';

    /**
     * @var MetaDataManager
     */
    private MetaDataManager $metaDataManager;

    /**
     * Invite constructor.
     *
     * @param MetaDataManager $metaDataManager
     * @param array           $query
     * @param array           $request
     * @param array           $attributes
     * @param array           $cookies
     * @param array           $files
     * @param array           $server
     * @param null            $content
     */
    public function __construct(
        MetaDataManager $metaDataManager,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->metaDataManager = $metaDataManager;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::PARAMETER_EMAIL     => ['required', 'email'],
            self::PARAMETER_META_DATA => ['array', $this->getMetaDataValidationRule()],
        ];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->get(self::PARAMETER_EMAIL);
    }

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        return $this->get(self::PARAMETER_META_DATA, []);
    }

    /**
     * @return \Closure
     */
    private function getMetaDataValidationRule(): \Closure
    {
        return function ($argument, $metaData, $fail): bool {
            if (!\is_array($metaData)) {
                $fail('validation.array');

                return false;
            }

            $project = $this->route('project');
            $validationErrors = $this->metaDataManager->validateMetaData($project, $metaData);
            if (!empty($validationErrors)) {
                $this->getValidatorInstance()->getMessageBag()->merge([
                    self::PARAMETER_META_DATA => $this->transformValidationErrors($validationErrors)
                ]);

                return false;
            }

            return true;
        };
    }

    /**
     * @param array $validationErrors
     *
     * @return array
     */
    private function transformValidationErrors(array $validationErrors): array
    {
        return \array_map(
            fn (array $errors) => \array_map(
                fn (string $error) => \sprintf('validation.%s', $error),
                $errors
            ),
            $validationErrors
        );
    }
}
