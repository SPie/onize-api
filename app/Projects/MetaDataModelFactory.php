<?php

namespace App\Projects;

use App\Users\UserModel;

/**
 * Interface MetaDataModelFactory
 *
 * @package App\Projects
 */
interface MetaDataModelFactory
{
    /**
     * @param ProjectModel $project
     * @param UserModel    $user
     * @param string       $name
     * @param string       $value
     *
     * @return MetaDataModel
     */
    public function create(ProjectModel $project, UserModel $user, string $name, string $value): MetaDataModel;
}
