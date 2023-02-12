<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LaravelDoctrine\Migrations\Schema\Builder;
use LaravelDoctrine\Migrations\Schema\Table;

final class Version20200515211800 extends AbstractMigration
{
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
    }

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

    public function down(Schema $schema): void
    {
        $this
            ->dropRefreshTokensTable($schema)
            ->dropInvitationsTable($schema)
            ->dropRolesPermissionsTable($schema)
            ->dropPermissionsTable($schema)
            ->dropMembersTable($schema)
            ->dropRolesTable($schema)
            ->dropMetaDataElementsTable($schema)
            ->dropProjectsTable($schema)
            ->dropUsersTable($schema);
    }

    private function dropUsersTable(Schema $schema): self
    {
        (new Builder($schema))->dropIfExists('users');

        return $this;
    }

    private function dropProjectsTable(Schema $schema): self
    {
        (new Builder($schema))->dropIfExists('projects');

        return $this;
    }

    private function dropMetaDataElementsTable(Schema $schema): self
    {
        (new Builder($schema))->dropIfExists('meta_data_elements');

        return $this;
    }

    private function dropRolesTable(Schema $schema): self
    {
        (new Builder($schema))->dropIfExists('roles');

        return $this;
    }

    private function dropMembersTable(Schema $schema): self
    {
        (new Builder($schema))->dropIfExists('members');

        return $this;
    }

    private function dropPermissionsTable(Schema $schema): self
    {
        (new Builder($schema))->dropIfExists('permissions');

        return $this;
    }

    private function dropRolesPermissionsTable(Schema $schema): self
    {
        (new Builder($schema))->dropIfExists('roles_permissions');

        return $this;
    }

    private function dropInvitationsTable(Schema $schema): self
    {
        (new Builder($schema))->dropIfExists('invitations');

        return $this;
    }

    private function dropRefreshTokensTable(Schema $schema): self
    {
        (new Builder($schema))->dropIfExists('refresh_tokens');

        return $this;
    }
}
