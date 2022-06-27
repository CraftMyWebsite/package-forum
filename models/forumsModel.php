<?php

namespace CMW\Model\Forums;

use CMW\Entity\Forums\categoryEntity;
use CMW\Entity\Forums\forumEntity;
use CMW\Entity\Forums\responseEntity;
use CMW\Entity\Forums\topicEntity;
use CMW\Model\manager;
use CMW\Model\Users\usersModel;

/**
 * Class: @forumsModel
 * @package Forums
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class forumsModel extends manager
{

    private usersModel $userModel;

    public function __construct()
    {

        $this->userModel = new usersModel();

    }

    /*=> GETTERS */

    public function getCategoryById(int $id): ?categoryEntity
    {
        $sql = "select * from cmw_forums_categories where forum_category_id = :category_id";

        $db = manager::dbConnect();

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

        $db = manager::dbConnect();

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
            $res["forum_category_id"],
            $res["forum_category_name"],
            $res["forum_category_description"] ?? "",
            $element
        );
    }

    public function getTopicById(int $id): ?topicEntity
    {

        $sql = "select * from cmw_forums_topics where forum_topic_id = :topic_id";

        $db = manager::dbConnect();

        $res = $db->prepare($sql);

        if (!$res->execute(array("topic_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = $this->userModel->getUserById($id);
        $forum = $this->getForumById($res["forum_id"]);

        if (is_null($forum) || is_null($user?->getUsername())) {
            return null;
        }

        return new topicEntity(
            $res["forum_topic_id"],
            $res["forum_topic_name"],
            $res["forum_topic_content"] ?? "",
            $forum,
            $user
        );
    }

    public function getResponseById(int $id): ?responseEntity
    {

        $sql = "select * from cmw_forums_response where forum_response_id = :response_id";

        $db = manager::dbConnect();

        $res = $db->prepare($sql);

        if (!$res->execute(array("response_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = $this->userModel->getUserById($id);
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
     * @return \CMW\Entity\Forums\categoryEntity[]
     */
    public function getCategories(): array
    {

        $sql = "select forum_category_id from cmw_forums_categories";
        $db = manager::dbConnect();

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
     * @return \CMW\Entity\Forums\forumEntity[]
     */
    public function getForumList($id, $isForumId = false): array
    {
        $sql = !$isForumId ? "select forum_id from cmw_forums WHERE forum_category_id = :forum_id" : "select forum_id from cmw_forums WHERE forum_subforum_id = :forum_id";
        $db = manager::dbConnect();

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

    /**
     * @return \CMW\Entity\Forums\topicEntity[]
     */
    public function getTopicByForum($id): array
    {

        $sql = "select forum_topic_id from cmw_forums_topics WHERE forum_id = :forum_id";
        $db = manager::dbConnect();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($top = $res->fetch()) {
            $toReturn[] = $this->getTopicById($top["forum_topic_id"]);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Forums\responseEntity[]
     */
    public function getResponseByTopic($id): array
    {
        $sql = "select forum_response_id from cmw_forums_response WHERE forum_topic_id = :forum_topic_id";
        $db = manager::dbConnect();
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

    /*=> CONSTRUCTORS */

    public function createCategory($name, $description): ?categoryEntity
    {

        $data = array(
            "category_name" => $name,
            "category_description" => $description
        );

        $sql = "INSERT INTO cmw_forums_categories(forum_category_name, forum_category_description) VALUES (:category_name, :category_description)";

        $db = manager::dbConnect();
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
            "forum_description" => $description,
            "reattached_Id" => $reattached_Id
        );

        $sql = "INSERT INTO cmw_forums(forum_id, forum_name, forum_description, (!$isCategory) ? forum_subforum_id : forum_category_id) VALUES (:forum_name, :forum_description, :reattached_Id)";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getForumById($db->lastInsertId());
        }

        return null;
    }

    public function createTopic($name, $content, $userId, $forumId): ?topicEntity
    {

        $data = array(
            "topic_name" => $name,
            "topic_content" => $content,
            "user_id" => $userId,
            "forum_id" => $forumId
        );

        $sql = "INSERT INTO cmw_forums_topics(forum_topic_name, forum_topic_content, user_id, forum_id) VALUES (:topic_name, :topic_content, :user_id, :forum_id)";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getTopicById($db->lastInsertId());
        }

        return null;
    }

    public function createResponse($content, $userId, $forumId): ?responseEntity
    {

        $data = array(
            "response_content" => $content,
            "user_id" => $userId,
            "forum_id" => $forumId
        );

        $sql = "INSERT INTO cmw_forums_response(forum_response_content, forum_topic_id, user_id) VALUES (:response_content, :user_id, :forum_id)";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getResponseById($db->lastInsertId());
        }

        return null;
    }


    /*=> DELETES */

    public function deleteCategory(categoryEntity $categoryModel): bool
    {
        $sql = "delete from cmw_forums_categories where forum_category_id = :category_id";

        $db = manager::dbConnect();

        return $db->prepare($sql)->execute(array("category_id" => $categoryModel->getId()));
    }


}