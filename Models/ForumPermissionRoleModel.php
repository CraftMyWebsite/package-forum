<?php

namespace CMW\Model\Forum;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Forum\ForumPermissionEntity;
use CMW\Entity\Forum\ForumPermissionRoleEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ForumPermissionRoleModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumPermissionRoleModel extends AbstractModel
{
    /**
     * @var \CMW\Model\Forum\ForumPermissionModel
     */
    private ForumPermissionModel $forumPermissionModel;

    public function __construct()
    {
        $this->forumPermissionModel = new ForumPermissionModel();
    }

    /**
     * @param $id
     * @return \CMW\Entity\Forum\ForumPermissionRoleEntity|null
     */
    public function getRoleById($id): ?ForumPermissionRoleEntity
    {
        $sql = 'SELECT * FROM cmw_forums_roles WHERE forums_role_id = :forums_role_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array('forums_role_id' => $id))) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return new ForumPermissionRoleEntity(
            $id,
            $res['forums_role_name'],
            $res['forums_role_description'],
            $res['forums_role_weight'],
            $res['forums_role_is_default'],
            $this->getPermissions($id)
        );
    }

    /**
     * @return \CMW\Entity\Forum\ForumPermissionRoleEntity []
     */
    public function getRole(): array
    {
        $sql = 'SELECT forums_role_id FROM cmw_forums_roles';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($role = $res->fetch()) {
            $toReturn[] = $this->getRoleById($role['forums_role_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $forumRoleId
     * @return ForumPermissionEntity[]
     */
    public function getPermissions(int $forumRoleId): array
    {
        $sql = 'SELECT forum_permission_id FROM cmw_forums_roles_permissions WHERE forum_role_id = :forum_role_id';
        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array('forum_role_id' => $forumRoleId))) {
            return array();
        }

        $toReturn = array();

        while ($perm = $res->fetch()) {
            $toReturn[] = $this->forumPermissionModel->getPermissionById($perm['forum_permission_id']);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Forum\ForumPermissionRoleEntity[]
     */
    public function getRolesByUser(int $userId): array
    {
        $sql = 'SELECT forums_role_id FROM cmw_forums_users_roles WHERE user_id = :user_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array('user_id' => $userId))) {
            return array();
        }

        $toReturn = array();

        while ($role = $req->fetch()) {
            $toReturn[] = $this->getRoleById($role['forums_role_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $userId
     * @return \CMW\Entity\Forum\ForumPermissionRoleEntity
     */
    public function getHighestRoleByUser(int $userId): ?ForumPermissionRoleEntity
    {
        $sql = 'SELECT cmw_forums_users_roles.forums_role_id 
                FROM cmw_forums_users_roles
                JOIN cmw_forums_roles ON cmw_forums_users_roles.forums_role_id = cmw_forums_roles.forums_role_id
                WHERE user_id = :user_id
                ORDER BY cmw_forums_roles.forums_role_weight DESC
                LIMIT 1';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array('user_id' => $userId))) {
            return null;
        }

        $res = $req->fetch();

        if (empty($res)) {
            return null;
        }

        return $this->getRoleById($res['forums_role_id']);
    }

    /**
     * @param string $name
     * @param int $weight
     * @param string $description
     * @return \CMW\Entity\Forum\ForumPermissionRoleEntity|null
     */
    public function addRole(string $name, int $weight, string $description): ?ForumPermissionRoleEntity
    {
        $data = array(
            'name' => $name,
            'weight' => $weight,
            'description' => $description
        );
        $sql = "INSERT INTO `cmw_forums_roles` (`forums_role_name`, `forums_role_description`, `forums_role_weight`, `forums_role_is_default`) VALUES (:name, :description, :weight, '0');";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getRoleById($id);
        }
        return null;
    }

    /**
     * @return void
     */
    public function editRole(string $name, int $weight, string $description, int $roleId): void
    {
        $sql = 'UPDATE cmw_forums_roles SET forums_role_name = :role_name, forums_role_description = :role_description, forums_role_weight = :role_weight  WHERE forums_role_id = :role_id';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(['role_id' => $roleId, 'role_name' => $name, 'role_description' => $description, 'role_weight' => $weight]);
    }

    /**
     * @param int $roleId
     * @return void
     */
    public function removeRole(int $roleId): void
    {
        $data = array(
            'role_id' => $roleId
        );
        $sql = 'DELETE FROM `cmw_forums_roles` WHERE `forums_role_id` = :role_id';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($data);
    }

    /**
     * @param int $roleId
     * @param int $permissionId
     * @return void
     */
    public function addRolePermissions(int $roleId, int $permissionId): void
    {
        $data = array(
            'permission_id' => $permissionId,
            'role_id' => $roleId
        );
        $sql = 'INSERT INTO `cmw_forums_roles_permissions` (`forum_permission_id`, `forum_role_id`) VALUES (:permission_id, :role_id);';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($data);
    }

    /**
     * @param int $roleId
     * @param int $permissionId
     * @return void
     */
    public function removeRolePermissions(int $roleId, int $permissionId): void
    {
        $data = array(
            'permission_id' => $permissionId,
            'role_id' => $roleId
        );
        $sql = 'DELETE FROM `cmw_forums_roles_permissions` WHERE `forum_permission_id` = :permission_id AND `forum_role_id` = :role_id';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($data);
    }

    /**
     * @return bool
     * @desc Check if we already have a config
     */
    public function roleHasPerm(int $roleId, int $permissionId): bool
    {
        $data = array(
            'permission_id' => $permissionId,
            'role_id' => $roleId
        );
        $sql = 'SELECT * FROM `cmw_forums_roles_permissions` WHERE forum_permission_id = :permission_id AND forum_role_id = :role_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return count($req->fetchAll());
        }

        return 0;
    }

    /**
     * @return void
     */
    public function changeUserRole(int $userId, int $roleId): void
    {
        $sql = 'UPDATE cmw_forums_users_roles SET forums_role_id = :role_id WHERE user_id = :user_id';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(['role_id' => $roleId, 'user_id' => $userId]);
    }

    /**
     * @return void
     */
    public function changeDefaultRole(int $id, string $question): void
    {
        if ($question === 'yes') {
            $this->updateDefaultRoleForAllUser($id);
        }
        $this->removePreviousDefaultRole();

        $sql = 'UPDATE cmw_forums_roles SET forums_role_is_default = 1 WHERE forums_role_id = :id';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array('id' => $id));
    }

    /**
     * @return void
     */
    public function removePreviousDefaultRole(): void
    {
        $sql = 'UPDATE cmw_forums_roles SET forums_role_is_default = 0 WHERE forums_role_is_default = 1';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute();
    }

    /**
     * @param $newDefaultRole
     * @return void
     */
    public function updateDefaultRoleForAllUser($newDefaultRole): void
    {
        foreach (UsersModel::getInstance()->getUsers() as $user) {
            $userId = $user->getId();
            if ($this->getHighestRoleByUser($userId)->isDefault()) {
                $sql = 'UPDATE cmw_forums_users_roles SET forums_role_id = :newDefaultRole WHERE user_id = :userId';
                $db = DatabaseManager::getInstance();
                $res = $db->prepare($sql);
                $res->execute(['newDefaultRole' => $newDefaultRole, 'userId' => $userId]);
            }
        }
    }

    /**
     * @param int $userId
     * @return void
     */
    public function addUserForumDefaultRoleOnRegister(int $userId): void
    {
        $data = array(
            'user_id' => $userId
        );

        $sql = 'INSERT INTO cmw_forums_users_roles (user_id, forums_role_id) SELECT :user_id, forums_role_id FROM cmw_forums_roles WHERE forums_role_is_default = 1;';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }

    /**
     * @param int $roleId
     * @return bool
     */
    public static function playerHasForumRole(int $roleId): bool
    {
        if (is_null(UsersSessionsController::getInstance()->getCurrentUser()?->getId())) {
            return false;
        }

        $roles = ForumPermissionRoleModel::getInstance()->getRolesByUser(UsersSessionsController::getInstance()->getCurrentUser()?->getId());
        foreach ($roles as $role) {
            if ($role->getId() === $roleId) {
                return true;
            }
        }

        return false;
    }
}
