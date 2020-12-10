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
    const PROPERTY_PROJECT = 'project';
    const PROPERTY_USER    = 'user';
    const PROPERTY_NAME    = 'name';
    const PROPERTY_VALUE   = 'value';

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
