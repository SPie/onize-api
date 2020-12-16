<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LaravelDoctrine\Migrations\Schema\Builder;
use LaravelDoctrine\Migrations\Schema\Table;

/**
 * Class Version20200515211800
 */
final class Version20200515211800 extends AbstractMigration
{
    //region Up calls

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this
            ->createUsersTable($schema)
            ->createProjectsTable($schema)
            ->createMetaDataElementsTable($schema)
            ->createRolesTable($schema)
            ->createRolesUsersTable($schema)
            ->createMetaDataTable($schema)
//            ->createLoginAttemptsTable($schema)
//            ->createProjectsTable($schema)
//            ->createProjectInvitesTable($schema)
//            ->createProjectMembersTable($schema)
//            ->createProjectMetaDataElements($schema)
        ;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createUsersTable(Schema $schema): self
    {
        (new Builder($schema))->create('users', function (Table $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->unique('uuid');
            $table->string('email');
            $table->unique('email');
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createProjectsTable(Schema $schema): self
    {
        (new Builder($schema))->create('projects', function (Table $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->unique('uuid');
            $table->string('label');
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createMetaDataElementsTable(Schema $schema): self
    {
        (new Builder($schema))->create('meta_data_elements', function (Table $table) {
            $table->increments('id');
            $table->integer('project_id', false, true);
            $table->foreign('projects', 'project_id', 'id');
            $table->string('name');
            $table->string('label');
            $table->boolean('required');
            $table->boolean('in_list');
            $table->string('type');
            $table->unique(['project_id', 'name']);
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createRolesTable(Schema $schema): self
    {
        (new Builder($schema))->create('roles', function (Table $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->string('label');
            $table->boolean('owner');
            $table->integer('project_id', false, true);
            $table->foreign('projects', 'project_id', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createRolesUsersTable(Schema $schema): self
    {
        (new Builder($schema))->create('roles_users', function (Table $table) {
            $table->increments('id');
            $table->integer('role_id', false, true);
            $table->foreign('roles', 'role_id', 'id');
            $table->integer('user_id', false, true);
            $table->foreign('users', 'user_id', 'id');
            $table->unique(['role_id', 'user_id']);
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createMetaDataTable(Schema $schema): self
    {
        (new Builder($schema))->create('meta_data', function (Table $table) {
            $table->increments('id');
            $table->integer('user_id', false, true);
            $table->foreign('users', 'user_id', 'id');
            $table->integer('project_id', false, true);
            $table->foreign('projects', 'project_id', 'id');
            $table->string('name');
            $table->string('value');
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createLoginAttemptsTable(Schema $schema): self
    {
        (new Builder($schema))->create('login_attempts', function (Table $table) {
            $table->increments('id');
            $table->string('ip_address');
            $table->string('identifier');
            $table->dateTime('attempted_at');
            $table->boolean('success');
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createProjectInvitesTable(Schema $schema): self
    {
        (new Builder($schema))->create('project_invites', function (Table $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->unique('uuid');
            $table->string('token');
            $table->unique('token');
            $table->string('email');
            $table->integer('project_id', false, true);
            $table->foreign('projects', 'project_id', 'id');
            $table->unique(['email', 'project_id']);
            $table->timestamps();
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createProjectMembersTable(Schema $schema): self
    {
        (new Builder($schema))->create('project_members', function (Table $table) {
            $table->increments('id');
            $table->integer('user_id', false, true);
            $table->foreign('users', 'user_id', 'id');
            $table->integer('project_id', false, true);
            $table->foreign('projects', 'project_id', 'id');
            $table->unique(['user_id', 'project_id']);
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createProjectMetaDataElements(Schema $schema): self
    {
        (new Builder($schema))->create('project_meta_data_elements', function (Table $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->unique('uuid');
            $table->string('label');
            $table->integer('project_id', false, true);
            $table->foreign('projects', 'project_id', 'id');
            $table->boolean('required');
            $table->boolean('in_list');
            $table->smallInteger('position');
            $table->string('field_type');
        });

        return $this;
    }

    //endregion

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        //no rollback
    }
}
