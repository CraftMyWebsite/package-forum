<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumCategoryEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Utils\Utils;
use PDO;

/**
 * Class: @ForumCategoryModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumCategoryModel extends AbstractModel
{
    /**
     * @return \CMW\Entity\Forum\ForumCategoryEntity[]
     */
    public function getCategories(): array
    {
        $sql = 'SELECT forum_category_id FROM cmw_forums_categories';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cat = $res->fetch()) {
            $toReturn[] = $this->getCategoryById($cat['forum_category_id']);
        }
        return $toReturn;
    }

    /**
     * @param $forum_id
     * @return \CMW\Entity\Forum\ForumCategoryEntity
     */
    function getCategoryByForumId($forum_id): ForumCategoryEntity
    {
        $sql = 'SELECT forum_category_id, forum_subforum_id FROM cmw_forums WHERE forum_id = :forum_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        $res->execute(array('forum_id' => $forum_id));

        $row = $res->fetch(PDO::FETCH_ASSOC);

        if (!empty($row['forum_category_id'])) {
            return ForumCategoryModel::getInstance()->getCategoryById($row['forum_category_id']);
        } elseif (!empty($row['forum_subforum_id'])) {
            return $this->getCategoryByForumId($row['forum_subforum_id']);
        }
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Forum\ForumCategoryEntity|null
     */
    public function getCategoryById(int $id): ?ForumCategoryEntity
    {
        $sql = 'SELECT * FROM cmw_forums_categories WHERE forum_category_id = :category_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('category_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ForumCategoryEntity(
            $res['forum_category_id'],
            $res['forum_category_name'],
            $res['forum_category_slug'],
            $res['forum_category_icon'],
            $res['forum_category_created'],
            $res['forum_category_updated'],
            $res['forum_category_description'] ?? null,
            $res['forum_category_restricted'],
            $this->getAllowedRoles($res['forum_category_id'])
        );
    }

    /**
     * @param string $slug
     * @return \CMW\Entity\Forum\ForumCategoryEntity|null
     */
    public function getCatBySlug(string $slug): ?ForumCategoryEntity
    {
        $sql = 'SELECT forum_category_id FROM cmw_forums_categories WHERE forum_category_slug = :cat_slug';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('cat_slug' => $slug))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        return $this->getCategoryById($res['forum_category_id']);
    }

    /**
     * @param string $name
     * @param string $icon
     * @param string $description
     * @param int $isRestricted
     * @return \CMW\Entity\Forum\ForumCategoryEntity|null
     */
    public function createCategory(string $name, string $icon, string $description, int $isRestricted): ?ForumCategoryEntity
    {
        $data = array(
            'category_name' => $name,
            'category_slug' => 'NOT_DEFINED',
            'category_icon' => $icon,
            'category_description' => $description,
            'category_restricted' => $isRestricted
        );

        $sql = 'INSERT INTO cmw_forums_categories(forum_category_name, forum_category_slug, forum_category_icon, forum_category_description, forum_category_restricted) VALUES (:category_name, :category_slug, :category_icon, :category_description, :category_restricted)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setCatSlug($id, $name);
            return $this->getCategoryById($id);
        }

        return null;
    }

    /**
     * @param int $id
     * @param string $name
     * @return void
     */
    private function setCatSlug(int $id, string $name): void
    {
        $slug = $this->generateSlug($id, $name);

        $data = array(
            'category_slug' => $slug,
            'category_id' => $id,
        );

        $sql = 'UPDATE cmw_forums_categories SET forum_category_slug = :category_slug WHERE forum_category_id = :category_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }

    /**
     * @param int $id
     * @param string $name
     * @return string
     */
    public function generateSlug(int $id, string $name): string
    {
        return Utils::normalizeForSlug($name) . "-$id";
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteForumCategoryGroupsAllowed(int $id): bool
    {
        $sql = 'DELETE FROM cmw_forums_categories_groups_allowed WHERE forum_category_id = :category_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array('category_id' => $id));
    }

    /**
     * @param int $roleId
     * @param int $categoryId
     * @return void
     */
    public function addForumCategoryGroupsAllowed(int $roleId, int $categoryId): void
    {
        $sql = 'INSERT INTO cmw_forums_categories_groups_allowed (forums_role_id, forum_category_id)
                VALUES (:role_id, :category_id)';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(['role_id' => $roleId, 'category_id' => $categoryId]);
    }

    /**
     * @param int $forumId
     * @return \CMW\Entity\Forum\ForumPermissionRoleEntity[]|null
     */
    public function getAllowedRoles(int $forumId): ?array
    {
        $sql = 'SELECT forums_role_id FROM cmw_forums_categories_groups_allowed WHERE forum_category_id = :id';
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $forumId])) {
            return null;
        }

        $roles = [];
        while ($role = $req->fetch()) {
            $roles[] = ForumPermissionRoleModel::getInstance()->getRoleById($role['forums_role_id']);
        }

        return $roles;
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $icon
     * @param string $description
     * @param int $isRestricted
     * @return \CMW\Entity\Forum\ForumCategoryEntity|null
     */
    public function editCategory(int $id, string $name, string $icon, string $description, int $isRestricted): ?ForumCategoryEntity
    {
        $data = array(
            'category_id' => $id,
            'category_name' => $name,
            'category_icon' => $icon,
            'category_description' => $description,
            'category_restricted' => $isRestricted
        );

        $sql = 'UPDATE cmw_forums_categories SET forum_category_name=:category_name, forum_category_icon=:category_icon, forum_category_description=:category_description, forum_category_restricted=:category_restricted WHERE forum_category_id=:category_id';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getCategoryById($id);
        }

        return null;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteCategory(int $id): bool
    {
        $sql = 'DELETE FROM cmw_forums_categories WHERE forum_category_id = :category_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array('category_id' => $id));
    }

    /**
     * @param int $categoryId
     * @return int
     */
    public function getNumberOfTopics(int $categoryId): int
    {
        $sql = "SELECT COUNT('forum_topic_id') AS `count` FROM cmw_forums_topics 
                JOIN cmw_forums ON cmw_forums_topics.forum_id = cmw_forums.forum_id
                JOIN cmw_forums_categories ON cmw_forums.forum_category_id = cmw_forums_categories.forum_category_id
                WHERE cmw_forums_categories.forum_category_id = :id";

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $categoryId])) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['count'] ?? 0;
    }

    /**
     * @param int $categoryId
     * @return int
     */
    public function getNumberOfMessages(int $categoryId): int
    {
        $sql = "SELECT COUNT('forum_response_id') AS `count` FROM cmw_forums_response 
                JOIN cmw_forums_topics ON cmw_forums_response.forum_topic_id = cmw_forums_topics.forum_topic_id
                JOIN cmw_forums ON cmw_forums_topics.forum_id = cmw_forums.forum_id
                JOIN cmw_forums_categories ON cmw_forums.forum_category_id = cmw_forums_categories.forum_category_id
                WHERE cmw_forums_categories.forum_category_id = :id";

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $categoryId])) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['count'] ?? 0;
    }
}
