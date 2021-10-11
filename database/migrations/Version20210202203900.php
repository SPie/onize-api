<?php

namespace Database\Migrations;

use App\Projects\PermissionModel;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20210202203900
 *
 * @package Database\Migrations
 */
final class Version20210202203900 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function up(Schema $schema): void
    {
        $this->createPermissions();
    }

    /**
     * @return $this
     */
    private function createPermissions(): self
    {
        $this->addSql('INSERT INTO `permissions` (`name`, `description`) VALUES
(\'' . PermissionModel::PERMISSION_PROJECTS_MEMBERS_SHOW . '\', \'Show projects members\'),
(\'' . PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT . '\', \'Invitations Management\'),
(\'' . PermissionModel::PERMISSION_PROJECTS_MEMBER_MANAGEMENT . '\', \'Members Management\'),
(\'' . PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT . '\', \'Roles Management\')');

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function down(Schema $schema): void
    {
        //no rollback
    }
}
