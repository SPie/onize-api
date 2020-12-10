<?php

namespace App\Projects;

use App\Users\UserModel;

/**
 * Class MetaDataDoctrineModelFactory
 *
 * @package App\Projects
 */
final class MetaDataDoctrineModelFactory implements MetaDataModelFactory
{
    /**
     * @param ProjectModel $project
     * @param UserModel    $user
     * @param string       $name
     * @param string       $value
     *
     * @return MetaDataModel
     */
    public function create(ProjectModel $project, UserModel $user, string $name, string $value): MetaDataModel
    {
        return new MetaDataDoctrineModel($project, $user, $name, $value);
    }
}
