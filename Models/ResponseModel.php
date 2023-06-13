<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumEntity;
use CMW\Entity\Forum\ResponseEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ResponseModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ResponseModel extends AbstractModel
{
    private UsersModel $userModel;
    private TopicModel $topicModel;

    public function __construct()
    {

        $this->userModel = new UsersModel();
        $this->topicModel = new TopicModel();

    }

    /**
     * @return \CMW\Entity\Forum\ResponseEntity[]
     */
    public function getResponseByTopic(int $id): array
    {
        $sql = "SELECT forum_response_id FROM cmw_forums_response WHERE forum_topic_id = :forum_topic_id AND forum_response_is_trash = 0";
        $db = DatabaseManager::getInstance();
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

    public function getResponseById(int $id): ?ResponseEntity
    {

        $sql = "SELECT * FROM cmw_forums_response WHERE forum_response_id = :response_id ";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("response_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = $this->userModel->getUserById($res["user_id"]);
        $topic = $this->topicModel->getTopicById($res["forum_topic_id"]);

        if (is_null($topic) || is_null($user?->getPseudo())) {
            return null;
        }

        return new ResponseEntity(
            $res["forum_response_id"],
            $res["forum_response_content"],
            $res["forum_response_is_trash"],
            $res["forum_response_trash_reason"],
            $res["forum_response_created"],
            $res["forum_response_updated"],
            $topic,
            $user
        );
    }

    public function countResponseInTopic(int $id): mixed
    {
        $sql = "SELECT COUNT(forum_response_id) as count FROM cmw_forums_response WHERE forum_topic_id = :forum_topic_id AND forum_response_is_trash = 0";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_topic_id" => $id))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    public function countResponseInTopicWithoutTrashFunction(int $id): mixed //never use this model without knowing what it really does!!
    {
        $sql = "SELECT COUNT(forum_response_id) as count FROM cmw_forums_response WHERE forum_topic_id = :forum_topic_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_topic_id" => $id))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    public function countResponseByUser(int $id): mixed
    {
        $sql = "SELECT COUNT(forum_response_id) as count FROM cmw_forums_response WHERE user_id = :user_id AND forum_response_is_trash = 0";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("user_id" => $id))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }


    public function createResponse(string $content, int $userId, int $topicId): ?ResponseEntity
    {

        $var = array(
            "response_content" => $content,
            "user_id" => $userId,
            "topic_id" => $topicId
        );

        $sql = "INSERT INTO cmw_forums_response(forum_response_content, forum_topic_id, user_id) VALUES (:response_content, :topic_id, :user_id)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $this->getResponseById($db->lastInsertId());
        }

        return null;
    }

    public function deleteResponse(int $id): bool
    {
        $sql = "DELETE FROM cmw_forums_response WHERE forum_response_id = :id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array("id" => $id))) {
            return false;
        }

        return $req->rowCount() === 1;
    }

    public function restoreResponse(int $id): ?ResponseEntity
    {
        $sql = "UPDATE `cmw_forums_response` SET `forum_response_is_trash` = '0' WHERE `forum_response_id` = :id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute(array("id" => $id))) {
            return $this->getResponseById($id);
        }

        return null;
    }

    public function trashResponse(int $id, int $reason): ?ResponseEntity
    {
        $sql = "UPDATE `cmw_forums_response` SET `forum_response_is_trash` = '1', `forum_response_trash_reason` = :reason WHERE `forum_response_id` = :id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute(array("id" => $id, "reason" => $reason))) {
            return $this->getResponseById($id);
        }

        return null;
    }

    public function getTrashResponse(): array
    {
        $sql = "SELECT * FROM `cmw_forums_response` WHERE `forum_response_is_trash` = 1 ORDER BY `cmw_forums_response`.`forum_response_updated` DESC";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($response = $res->fetch()) {
            $toReturn[] = $this->getResponseById($response["forum_response_id"]);
        }

        return $toReturn;

    }

    public function getLatestResponseInTopic(int $topicId): ?ResponseEntity
    {
        $sql = "SELECT * FROM `cmw_forums_response` 
                                           WHERE `forum_topic_id` = :forum_topic_id AND forum_response_is_trash = 0
                                           ORDER BY `cmw_forums_response`.`forum_response_id` 
                                           DESC limit 1 offset 0";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_topic_id" => $topicId))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res){
            return null;
        }

        $user = $this->userModel->getUserById($res["user_id"]);
        $topic = $this->topicModel->getTopicById($res["forum_topic_id"]);

        if (is_null($topic) || is_null($user?->getPseudo())) {
            return null;
        }

        return new ResponseEntity(
            $res["forum_response_id"],
            $res["forum_response_content"],
            $res["forum_response_is_trash"],
            $res["forum_response_trash_reason"],
            $res["forum_response_created"],
            $res["forum_response_updated"],
            $topic,
            $user
        );
    }

    /**
     * @param int $forumId
     * @return \CMW\Entity\Forum\ResponseEntity|null
     */
    public function getLatestResponseInForum(int $forumId): ?ResponseEntity
    {
        $sql = "SELECT cmw_forums_response.forum_topic_id, cmw_forums_response.*
                FROM cmw_forums_response
                JOIN cmw_forums_topics ON cmw_forums_response.forum_topic_id = cmw_forums_topics.forum_topic_id
                JOIN cmw_forums ON cmw_forums_topics.forum_id = cmw_forums.forum_id
                WHERE cmw_forums_topics.forum_id IN
                (SELECT cmw_forums.forum_id FROM cmw_forums WHERE cmw_forums.forum_subforum_id = :forum_id)
                OR cmw_forums.forum_id = :forum_id_2 AND forum_response_is_trash = 0
                ORDER BY cmw_forums_response.forum_response_id DESC LIMIT 1";

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(["forum_id" => $forumId, "forum_id_2" => $forumId])){
            return null;
        }

        $res = $req->fetch();

        if (!$res){
            return null;
        }

        $user = $this->userModel->getUserById($res["user_id"]);
        $topic = $this->topicModel->getTopicById($res["forum_topic_id"]);

        if (is_null($topic) || is_null($user?->getPseudo())) {
            return null;
        }

        return new ResponseEntity(
            $res["forum_response_id"],
            $res["forum_response_content"],
            $res["forum_response_is_trash"],
            $res["forum_response_trash_reason"],
            $res["forum_response_created"],
            $res["forum_response_updated"],
            $topic,
            $user
        );
    }

}