<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\categoryEntity;
use CMW\Entity\Forum\forumEntity;
use CMW\Entity\Forum\responseEntity;
use CMW\Entity\Forum\topicEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Model\users\UsersModel;
use CMW\Utils\Utils;

/**
 * Class: @ForumModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumModel extends DatabaseManager
{

    private UsersModel $userModel;

    public function __construct()
    {

        $this->userModel = new UsersModel();

    }

    /*==> UTILS */

    private function generateSlug(int $id, string $name): string
    {
        return Utils::normalizeForSlug($name) . ".$id";
    }

    /*=> GETTERS */

    public function getCategoryById(int $id): ?categoryEntity
    {
        $sql = "select * from cmw_forums_categories where forum_category_id = :category_id";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("category_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new categoryEntity(
            $res["forum_category_id"],
            $res["forum_category_name"],
            $res["forum_category_description"] ?? ""
        );
    }

    public function getForumById(int $id): ?forumEntity
    {
        $sql = "select * from cmw_forums where forum_id = :forum_id";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $element = is_null($res["forum_subforum_id"]) ? $this->getCategoryById($res["forum_category_id"]) : $this->getForumById($res["forum_subforum_id"]);

        if (is_null($element)) {
            return null;
        }

        return new forumEntity(
            $res["forum_id"],
            $res["forum_name"],
            $res["forum_description"] ?? "",
            $res["forum_slug"],
            $element
        );
    }

    public function getTopicById(int $id): ?topicEntity
    {

        $sql = "select * from cmw_forums_topics where forum_topic_id = :topic_id";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("topic_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $user = $this->userModel->getUserById($res["user_id"]);
        $forum = $this->getForumById($res["forum_id"]);

        if (is_null($forum) || is_null($user?->getUsername())) {
            return null;
        }

        return new topicEntity(
            $res["forum_topic_id"],
            $res["forum_topic_name"],
            $res["forum_topic_content"] ?? "",
            $res["forum_topic_slug"],
            $res["forum_topic_pinned"],
            $forum,
            $user
        );
    }

    public function getResponseById(int $id): ?responseEntity
    {

        $sql = "select * from cmw_forums_response where forum_response_id = :response_id ";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("response_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = $this->userModel->getUserById($res["user_id"]);
        $topic = $this->getTopicById($res["forum_topic_id"]);

        if (is_null($topic) || is_null($user?->getUsername())) {
            return null;
        }

        return new responseEntity(
            $res["forum_response_id"],
            $res["forum_response_content"],
            $topic,
            $user
        );
    }

    /**
     * @return \CMW\Entity\Forum\categoryEntity[]
     */
    public function getCategories(): array
    {

        $sql = "select forum_category_id from cmw_forums_categories";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cat = $res->fetch()) {
            $toReturn[] = $this->getCategoryById($cat["forum_category_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Forum\forumEntity[]
     */
    public function getForums(): array
    {
        $sql = "select forum_id from cmw_forums";
        $db = self::getInstance();

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

    /**
     * @return \CMW\Entity\Forum\forumEntity[]
     */
    public function getForumByParent($id, $isForumId = false): array
    {
        $sql = !$isForumId ? "select forum_id from cmw_forums WHERE forum_category_id = :forum_id" : "select forum_id from cmw_forums WHERE forum_subforum_id = :forum_id";
        $db = self::getInstance();

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

    public function getForumBySlug($slug): ?forumEntity
    {
        $sql = "select forum_id from cmw_forums WHERE forum_slug = :forum_slug";

        $db = self::getInstance();

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

    public function getTopicBySlug($slug): ?topicEntity
    {
        $sql = "select forum_topic_id from cmw_forums_topics WHERE forum_topic_slug = :topic_slug";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("topic_slug" => $slug))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        return $this->getTopicById($res["forum_topic_id"]);
    }

    /**
     * @return \CMW\Entity\Forum\topicEntity[]
     */
    public function getTopicByForum($id): array
    {

        $sql = "select forum_topic_id from cmw_forums_topics WHERE forum_id = :forum_id ORDER BY forum_topic_pinned desc";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($top = $res->fetch()) {
            $topic = $this->getTopicById($top["forum_topic_id"]);
            if (!is_null($topic)) {
                $toReturn[] = $topic;
            }
        }

        return $toReturn;
    }

    public function countTopicInForum($id): mixed
    {
        $sql = "select COUNT(forum_topic_id) from cmw_forums_topics WHERE forum_id = :forum_id";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_id" => $id))) {
            return 0;
        }

        $return = $res->fetch(0);
        return implode($return);
    }

    /**
     * @return \CMW\Entity\Forum\responseEntity[]
     */
    public function getResponseByTopic($id): array
    {
        $sql = "select forum_response_id from cmw_forums_response WHERE forum_topic_id = :forum_topic_id";
        $db = self::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_topic_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($resp = $res->fetch()) {
            $toReturn[] = $this->getResponseById($resp["forum_response_id"]);
        }

        return $toReturn;
    }

    public function countResponseInTopic($id): mixed
    {
        $sql = "select COUNT(forum_response_id) from cmw_forums_response WHERE forum_topic_id = :forum_topic_id";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_topic_id" => $id))) {
            return 0;
        }

        return $res->fetch(0);
    }


    public function hasSubForums($id): bool
    {
        $sql = "select COUNT(forum_id) from cmw_forums WHERE forum_subforum_id = :forum_id";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_id" => $id))) {
            return false;
        }

        return (bool)$res->fetch(0);
    }

    /*=> CONSTRUCTORS */

    public function createCategory($name, $description): ?categoryEntity
    {

        $data = array(
            "category_name" => $name,
            "category_description" => $description
        );

        $sql = "INSERT INTO cmw_forums_categories(forum_category_name, forum_category_description) VALUES (:category_name, :category_description)";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return new categoryEntity($id, $name, $description);
        }

        return null;
    }

    public function createForum($name, $description, $reattached_Id, $isCategory = true): ?forumEntity
    {
        $data = array(
            "forum_name" => $name,
            "forum_slug" => "NOT_DEFINED",
            "forum_description" => $description,
            "reattached_Id" => $reattached_Id
        );

        $sql = "INSERT INTO cmw_forums(forum_id, forum_name, forum_slug, forum_description, (!$isCategory) ? forum_subforum_id : forum_category_id) VALUES (:forum_name, :forum_slug, :forum_description, :reattached_Id)";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $this->setForumSlug($db->lastInsertId(), $name);
            return $this->getForumById($db->lastInsertId());
        }

        return null;
    }

    private function setForumSlug($id, $name): void
    {
        $slug = $this->generateSlug($id, $name);

        $data = array(
            "forum_slug" => $slug,
            "forum_id" => $id,
        );

        $sql = "UPDATE cmw_forums SET forum_slug = :forum_slug WHERE forum_id = :forum_id";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }

    public function createTopic($name, $content, $userId, $forumId): ?topicEntity
    {

        $data = array(
            "topic_name" => $name,
            "topic_content" => $content,
            "topic_slug" => "NOT DEFINED",
            "user_id" => $userId,
            "forum_id" => $forumId
        );

        $sql = "INSERT INTO cmw_forums_topics(forum_topic_name, forum_topic_content, forum_topic_slug, user_id, forum_id) VALUES (:topic_name, :topic_content, :topic_slug, :user_id, :forum_id)";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $this->setTopicSlug($db->lastInsertId(), $name);
            return $this->getTopicById($db->lastInsertId());
        }

        return null;
    }

    private function setTopicSlug(int $id, string $name): void
    {
        $slug = $this->generateSlug($id, $name);

        $data = array(
            "topic_slug" => $slug,
            "topic_id" => $id,
        );

        $sql = "UPDATE cmw_forums_topics SET forum_topic_slug = :topic_slug WHERE forum_topic_id = :topic_id";

        $db = self::getInstance();

        $req = $db->prepare($sql);

        $req->execute($data);
    }

    public function createResponse(string $content, int $userId, int $topicId): ?responseEntity
    {

        $var = array(
            "response_content" => $content,
            "user_id" => $userId,
            "topic_id" => $topicId
        );

        $sql = "INSERT INTO cmw_forums_response(forum_response_content, forum_topic_id, user_id) VALUES (:response_content, :topic_id, :user_id)";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $this->getResponseById($db->lastInsertId());
        }

        return null;
    }


    /*=> DELETES */

    public function deleteCategory(categoryEntity $categoryModel): bool
    {
        $sql = "delete from cmw_forums_categories where forum_category_id = :category_id";

        $db = self::getInstance();

        return $db->prepare($sql)->execute(array("category_id" => $categoryModel->getId()));
    }


}