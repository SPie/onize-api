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
            ->createMembersTable($schema)
            ->createPermissionsTable($schema)
            ->createRolesPermissionsTable($schema)
            ->createInvitationsTable($schema)
            ->createRefreshTokensTable($schema);
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
            $table->json('meta_data');
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
            $table->unique('uuid');
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
    private function createMembersTable(Schema $schema): self
    {
        (new Builder($schema))->create('members', function (Table $table) {
            $table->increments('id');
            $table->integer('user_id', false, true);
            $table->foreign('users', 'user_id');
            $table->integer('role_id', false, true);
            $table->foreign('roles', 'role_id');
            $table->json('meta_data');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'role_id']);
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createPermissionsTable(Schema $schema): self
    {
        (new Builder($schema))->create('permissions', function (Table $table) {
            $table->increments('id');
            $table->string('name');
            $table->unique('name');
            $table->string('description');
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createRolesPermissionsTable(Schema $schema): self
    {
        (new Builder($schema))->create('roles_permissions', function (Table $table) {
            $table->increments('id');
            $table->integer('role_id', false, true);
            $table->foreign('roles', 'role_id', 'id');
            $table->integer('permission_id', false, true);
            $table->foreign('permissions', 'permission_id', 'id');
        });

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function createInvitationsTable(Schema $schema): self
    {
        (new Builder($schema))->create('invitations', function (Table $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->unique('uuid');
            $table->string('email');
            $table->integer('role_id', false, true);
            $table->foreign('roles', 'role_id');
            $table->dateTime('valid_until');
            $table->dateTime('accepted_at')->setNotnull(false)->setDefault(null);
            $table->dateTime('declined_at')->setNotnull(false)->setDefault(null);
            $table->json('meta_data');
            $table->timestamps();
        });

        return $this;
    }

    private function createRefreshTokensTable(Schema $schema): self
    {
        (new Builder($schema))->create('refresh_tokens', function (Table $table) {
            $table->increments('id');
            $table->string('refresh_token_id');
            $table->dateTime('revoked_at')->setNotnull(false)->setDefault(null);
            $table->timestamps();
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
