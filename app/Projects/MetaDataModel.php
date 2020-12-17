<?php

namespace App\Projects;

use App\Models\Model;
use App\Users\UserModel;

/**
 * Interface MetaDataModel
 *
 * @package App\Projects
 */
interface MetaDataModel extends Model
{
    public const PROPERTY_PROJECT = 'project';
    public const PROPERTY_USER    = 'user';
    public const PROPERTY_NAME    = 'name';
    public const PROPERTY_VALUE   = 'value';

    /**
     * @return ProjectModel
     */
    public function getProject(): ProjectModel;

    /**
     * @return UserModel
     */
    public function getUser(): UserModel;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue(string $value): self;

    /**
     * @return string
     */
    public function getValue(): string;
}
