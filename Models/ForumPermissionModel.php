<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumPermissionEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ForumPermissionModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumPermissionModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Forum\ForumPermissionEntity|null
     */
    public function getPermissionById(int $id): ?ForumPermissionEntity
    {
        $sql = 'SELECT * FROM cmw_forums_permissions WHERE forum_permission_id = :forum_permission_id';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('forum_permission_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ForumPermissionEntity(
            $res['forum_permission_id'],
            $res['forum_permission_parent_id'],
            $res['forum_permission_code']
        );
    }

    /**
     * @return \CMW\Entity\Forum\ForumPermissionEntity[]
     */
    public function getPermissions(): array
    {
        $sql = 'SELECT forum_permission_id FROM cmw_forums_permissions';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($perm = $res->fetch()) {
            $toReturn[] = $this->getPermissionById($perm['forum_permission_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $userId
     * @param string $permissionCode
     * @return bool
     */
    public function hasForumPermission(int $userId, string $permissionCode): bool
    {
        foreach ($this->getPermissionsByUser($userId) as $userPermission) {
            if ($userPermission->getCode() === 'operator') {
                return true;
            }
            if ($permissionCode === $userPermission->getCode()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \CMW\Entity\Users\PermissionEntity[]
     */
    public function getPermissionsByUser(int $userId): array
    {
        $roles = ForumPermissionRoleModel::getInstance()->getRolesByUser($userId);

        $rolesModel = new ForumPermissionRoleModel();

        $toReturn = array();
        foreach ($roles as $role) {
            $permissions = $rolesModel->getPermissions($role->getId());
            foreach ($permissions as $permission) {
                $toReturn[] = $permission;
            }
        }

        return $toReturn;
    }
}
