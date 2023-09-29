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
            return array();
        }

        $toReturn = array();

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

        if (!$res->execute(array("forum_id" => $id))) {
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
            $res["forum_slug"],
            $res["forum_created"],
            $res["forum_updated"],
            $element
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

        if (!$res->execute(array("forum_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($forum = $res->fetch()) {
            $toReturn[] = $this->getForumById($forum["forum_id"]);
        }

        return $toReturn;
    }

    public function getSubforumByForum(int $id): array
    {
        $sql = "SELECT forum_id FROM cmw_forums WHERE forum_subforum_id = :forum_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($forum = $res->fetch()) {
            $toReturn[] = $this->getForumById($forum["forum_id"]);
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

        if (!$res->execute(array("forum_id" => $forumId))) {
            return array();
        }

        $toReturn = array();

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

        if (!$res->execute(array("forum_slug" => $slug))) {
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
        $sql = "SELECT COUNT(cmw_forums_topics.forum_topic_id) AS count FROM cmw_forums_topics
                JOIN cmw_forums ON cmw_forums_topics.forum_id = cmw_forums.forum_id
                WHERE cmw_forums_topics.forum_id IN 
                      (SELECT cmw_forums.forum_id FROM cmw_forums WHERE cmw_forums.forum_subforum_id = :forum_id)
                OR cmw_forums.forum_id = :forum_id_2";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_id" => $id, "forum_id_2" => $id))) {
            return 0;
        }

        return $res->fetch(0)['count'];
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
        $sql = "SELECT COUNT('forum_response_id') AS `count`
                FROM cmw_forums_response
                JOIN cmw_forums_topics ON cmw_forums_response.forum_topic_id = cmw_forums_topics.forum_topic_id
                JOIN cmw_forums ON cmw_forums_topics.forum_id = cmw_forums.forum_id
                WHERE cmw_forums_topics.forum_id IN
                (SELECT cmw_forums.forum_id FROM cmw_forums WHERE cmw_forums.forum_subforum_id = :forum_id)
                OR cmw_forums.forum_id = :forum_id_2 AND forum_response_is_trash = 0";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_id" => $id, "forum_id_2" => $id))) {
            return 0;
        }

        return $res->fetch(0)['count'];
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

        if (!$res->execute(array("forum_id" => $id))) {
            return false;
        }

        return (bool)$res->fetch(0);
    }

    public function createForum(string $name, string $icon, string $description, int $reattached_Id): ?ForumEntity
    {
        $data = array(
            "forum_name" => $name,
            "forum_icon" => $icon,
            "forum_slug" => "NOT_DEFINED",
            "forum_description" => $description,
            "reattached_Id" => $reattached_Id
        );

        $sql = "INSERT INTO cmw_forums(forum_name, forum_icon, forum_slug, forum_description, forum_category_id)
                VALUES (:forum_name, :forum_icon, :forum_slug, :forum_description, :reattached_Id)";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setForumSlug($id, $name);
            return $this->getForumById($id);
        }

        return null;
    }

    private function setForumSlug(int $id, string $name): void
    {
        $slug = $this->generateSlug($id, $name);

        $data = array(
            "forum_slug" => $slug,
            "forum_id" => $id,
        );

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

    public function createSubForum(string $name, string $icon, string $description, int $reattached_Id): ?ForumEntity
    {
        $data = array(
            "forum_name" => $name,
            "forum_icon" => $icon,
            "forum_slug" => "NOT_DEFINED",
            "forum_description" => $description,
            "reattached_Id" => $reattached_Id
        );

        $sql = "INSERT INTO cmw_forums(forum_name, forum_icon, forum_slug, forum_description,  forum_subforum_id)
                VALUES (:forum_name, :forum_icon, :forum_slug, :forum_description, :reattached_Id)";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setForumSlug($id, $name);
            return $this->getForumById($id);
        }

        return null;
    }

    public function editForum(int $id, string $name, string $icon, string $description, int $reattached_Id, bool $isCategory = true): ?ForumEntity
    {
        $data = array(
            "forum_id" => $id,
            "forum_name" => $name,
            "forum_icon" => $icon,
            "forum_slug" => "NOT_DEFINED",
            "forum_description" => $description,
            "reattached_Id" => $reattached_Id
        );

        $sql = "UPDATE cmw_forums SET forum_name=:forum_name, forum_icon=:forum_icon, forum_slug=:forum_slug, forum_description=:forum_description, forum_category_id=:reattached_Id WHERE forum_id=:forum_id";


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

        return $db->prepare($sql)->execute(array("forum_id" => $id));
    }

}