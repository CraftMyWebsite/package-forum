<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Utils\Utils;

/**
 * Class: @ForumModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumModel extends AbstractModel
{
    private ForumCategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new ForumCategoryModel();
    }

    /*==> UTILS */

    /**
     * @return \CMW\Entity\Forum\ForumEntity[]
     */
    public function getForums(): array
    {
        $sql = "SELECT forum_id FROM cmw_forums";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($for = $res->fetch()) {
            $toReturn[] = $this->getForumById($for["forum_id"]);
        }

        return $toReturn;
    }

    /*=> GETTERS */

    public function getForumById(int $id): ?ForumEntity
    {
        $sql = "SELECT * FROM cmw_forums WHERE forum_id = :forum_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["forum_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        $element = is_null($res["forum_subforum_id"]) ? $this->categoryModel->getCategoryById($res["forum_category_id"]) : $this->getForumById($res["forum_subforum_id"]);

        if (is_null($element)) {
            return null;
        }

        return new ForumEntity(
            $res["forum_id"],
            $res["forum_name"],
            $res["forum_icon"],
            $res["forum_description"] ?? "",
            $res["forum_restricted"],
            $res["forum_disallow_topics"],
            $res["forum_slug"],
            $res["forum_created"],
            $res["forum_updated"],
            $element,
            $this->getAllowedRoles($res["forum_id"])
        );
    }

    /**
     * @return \CMW\Entity\Forum\ForumEntity[]
     */
    public function getForumByCat(int $id): array
    {
        $sql = "SELECT forum_id FROM cmw_forums WHERE forum_category_id = :forum_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["forum_id" => $id])) {
            return [];
        }

        $toReturn = [];

        while ($forum = $res->fetch()) {
            $toReturn[] = $this->getForumById($forum["forum_id"]);
        }

        return $toReturn;
    }

    /**
     * @param int $id
     * @return ForumEntity[]
     */
    public function getSubforumByForum(int $id): array
    {
        $sql = "SELECT forum_id FROM cmw_forums WHERE forum_subforum_id = :forum_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["forum_id" => $id])) {
            return [];
        }

        $toReturn = [];

        while ($forum = $res->fetch()) {
            $toReturn[] = $this->getForumById($forum["forum_id"]);
        }

        return $toReturn;
    }


    public function getSubsForums(int $forumId): array
    {
        return $this->getSubforumsRecursively($forumId, 1);
    }

    private function getSubforumsRecursively(int $forumId, int $depth): array
    {
        $toReturn = [];
        $subforums = $this->getSubforumByForum($forumId);

        foreach ($subforums as $subForumObj) {
            $subForumData = [
                'subforum' => $subForumObj,
                'depth' => $depth,
            ];
            $toReturn[] = $subForumData;

            $subToReturn = $this->getSubforumsRecursively($subForumObj->getId(), $depth + 1);
            $toReturn = [...$toReturn, ...$subToReturn];
        }

        return $toReturn;
    }


    public function getParentByForumId(int $forumId): array
    {
        $sql = "WITH RECURSIVE ForumHierarchy AS (
  SELECT forum_id, forum_subforum_id
  FROM cmw_forums
  WHERE forum_id = :forum_id
  UNION ALL
  SELECT f.forum_id, f.forum_subforum_id
  FROM cmw_forums f
  INNER JOIN ForumHierarchy fh ON f.forum_id = fh.forum_subforum_id
)
SELECT DISTINCT forum_id
FROM ForumHierarchy ORDER BY forum_id ASC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["forum_id" => $forumId])) {
            return [];
        }

        $toReturn = [];

        while ($forum = $res->fetch()) {
            $toReturn[] = $this->getForumById($forum["forum_id"]);
        }

        return $toReturn;
    }

    //TODO : Tenté d'améliorer ceci
    public function getChildForumByCatId(int $catId): array
    {
        $sql = "WITH RECURSIVE ForumHierarchy AS (
  SELECT forum_id, forum_name, forum_category_id, forum_subforum_id
  FROM cmw_forums
  WHERE `forum_category_id` = :cat_id

  UNION ALL

  SELECT f.forum_id, f.forum_name, f.forum_category_id, f.forum_subforum_id
  FROM ForumHierarchy fh
  JOIN cmw_forums f ON fh.forum_id = f.forum_subforum_id
)
SELECT * FROM ForumHierarchy ORDER BY forum_id ASC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["cat_id" => $catId])) {
            return [];
        }

        $toReturn = [];

        while ($forum = $res->fetch()) {
            $toReturn[] = $this->getForumById($forum["forum_id"]);
        }

        return $toReturn;
    }

    public function getForumBySlug(string $slug): ?ForumEntity
    {
        $sql = "SELECT forum_id FROM cmw_forums WHERE forum_slug = :forum_slug";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["forum_slug" => $slug])) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        return $this->getForumById($res["forum_id"]);
    }

    public function countTopicInForum(int $id): mixed
    {
        $sql = "WITH RECURSIVE ForumHierarchy AS (
  SELECT forum_id, forum_subforum_id
  FROM cmw_forums
  WHERE forum_id = :forum_id

  UNION ALL

  SELECT f.forum_id, f.forum_subforum_id
  FROM cmw_forums f
  INNER JOIN ForumHierarchy fh ON f.forum_subforum_id = fh.forum_id
)

SELECT COUNT(DISTINCT ft.forum_topic_id) AS total_forum_topics
FROM cmw_forums_topics ft
INNER JOIN ForumHierarchy fh ON ft.forum_id = fh.forum_id;";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["forum_id" => $id])) {
            return 0;
        }

        return $res->fetch(0)['total_forum_topics'];
    }

    public function countAllTopicsInAllForum(): int
    {
        $sql = "SELECT COUNT(cmw_forums_topics.forum_topic_id) AS `count` FROM cmw_forums_topics";

        $db = DatabaseManager::getInstance();

        $res = $db->query($sql);

        return $res->fetch()['count'] ?? 0;
    }

    public function countMessagesInForum(int $id): mixed
    {
        $sql = "WITH RECURSIVE ForumHierarchy AS (
  SELECT forum_id, forum_subforum_id, forum_id AS root_forum_id
  FROM cmw_forums
  WHERE forum_id = :forum_id
  
  UNION ALL
  
  SELECT f2.forum_id, f2.forum_subforum_id, fh.root_forum_id
  FROM cmw_forums f2
  INNER JOIN ForumHierarchy fh ON f2.forum_subforum_id = fh.forum_id
)

SELECT COUNT(r.forum_response_id) AS total_responses
FROM ForumHierarchy fh
LEFT JOIN cmw_forums_topics t ON fh.forum_id = t.forum_id
LEFT JOIN cmw_forums_response r ON t.forum_topic_id = r.forum_topic_id;";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["forum_id" => $id])) {
            return 0;
        }

        return $res->fetch(0)['total_responses'];
    }

    public function countAllMessagesInAllForum(): int
    {
        $sql = "SELECT COUNT('forum_response_id') AS `count` FROM cmw_forums_response";

        $db = DatabaseManager::getInstance();

        $res = $db->query($sql);

        return $res->fetch()['count'] ?? 0;
    }

    public function hasSubForums(int $id): bool
    {
        $sql = "SELECT COUNT(forum_id) FROM cmw_forums WHERE forum_subforum_id = :forum_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["forum_id" => $id])) {
            return false;
        }

        return (bool)$res->fetch(0);
    }

    public function createForum(string $name, string $icon, string $description, int $isRestricted, int $disallowTopics, int $reattached_Id): ?ForumEntity
    {
        $data = [
            "forum_name" => $name,
            "forum_icon" => $icon,
            "forum_slug" => "NOT_DEFINED",
            "forum_description" => $description,
            "is_restricted" => $isRestricted,
            "disallow_topics" => $disallowTopics,
            "reattached_Id" => $reattached_Id,
        ];

        $sql = "INSERT INTO cmw_forums(forum_name, forum_icon, forum_slug, forum_description, forum_restricted, forum_disallow_topics, forum_category_id)
                VALUES (:forum_name, :forum_icon, :forum_slug, :forum_description,:is_restricted, :disallow_topics, :reattached_Id)";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setForumSlug($id, $name);
            return $this->getForumById($id);
        }

        return null;
    }

    public function addForumGroupsAllowed(int $roleId, int $forumId): void
    {
        $sql = "INSERT INTO cmw_forums_groups_allowed (forums_role_id, forum_id)
                VALUES (:role_id, :forum_id)";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(['role_id' => $roleId, 'forum_id' => $forumId]);
    }

    public function deleteForumGroupsAllowed(int $id): bool
    {
        $sql = "DELETE FROM cmw_forums_groups_allowed WHERE forum_id = :forum_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(["forum_id" => $id]);
    }

    /**
     * @param int $forumId
     * @return \CMW\Entity\Forum\ForumPermissionRoleEntity[]|null
     */
    public function getAllowedRoles(int $forumId): ?array
    {
        $sql = "SELECT forums_role_id FROM cmw_forums_groups_allowed WHERE forum_id = :id";
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

    private function setForumSlug(int $id, string $name): void
    {
        $slug = $this->generateSlug($id, $name);

        $data = [
            "forum_slug" => $slug,
            "forum_id" => $id,
        ];

        $sql = "UPDATE cmw_forums SET forum_slug = :forum_slug WHERE forum_id = :forum_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }

    public function generateSlug(int $id, string $name): string
    {
        return Utils::normalizeForSlug($name) . "-$id";
    }


    /*=> CONSTRUCTORS */

    public function createSubForum(string $name, string $icon, string $description, int $isRestricted, int $disallowTopics, int $reattached_Id): ?ForumEntity
    {
        $data = [
            "forum_name" => $name,
            "forum_icon" => $icon,
            "forum_slug" => "NOT_DEFINED",
            "forum_description" => $description,
            "is_restricted" => $isRestricted,
            "disallow_topics" => $disallowTopics,
            "reattached_Id" => $reattached_Id,
        ];

        $sql = "INSERT INTO cmw_forums(forum_name, forum_icon, forum_slug, forum_description, forum_restricted, forum_disallow_topics,  forum_subforum_id)
                VALUES (:forum_name, :forum_icon, :forum_slug, :forum_description,:is_restricted, :disallow_topics, :reattached_Id)";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setForumSlug($id, $name);
            return $this->getForumById($id);
        }

        return null;
    }

    public function editForum(int $id, string $name, string $icon, string $description, int $isRestricted, int $disallowTopics): ?ForumEntity
    {
        $data = [
            "forum_id" => $id,
            "forum_name" => $name,
            "forum_icon" => $icon,
            "forum_slug" => "NOT_DEFINED",
            "forum_description" => $description,
            "is_restricted" => $isRestricted,
            "disallow_topics" => $disallowTopics,
        ];

        $sql = "UPDATE cmw_forums SET forum_name=:forum_name, forum_icon=:forum_icon, forum_slug=:forum_slug, forum_description=:forum_description, forum_restricted=:is_restricted, forum_disallow_topics=:disallow_topics WHERE forum_id=:forum_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $this->setForumSlug($id, $name);
        }

        return null;
    }

    public function deleteForum(int $id): bool
    {
        $sql = "DELETE FROM cmw_forums WHERE forum_id = :forum_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(["forum_id" => $id]);
    }

}