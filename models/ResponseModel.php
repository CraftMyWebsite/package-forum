<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\responseEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ResponseModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ResponseModel extends DatabaseManager
{
    private UsersModel $userModel;
    private TopicModel $topicModel;

    public function __construct()
    {

        $this->userModel = new UsersModel();
        $this->topicModel = new TopicModel();

    }

    /**
     * @return \CMW\Entity\Forum\responseEntity[]
     */
    public function getResponseByTopic(int $id): array
    {
        $sql = "SELECT forum_response_id FROM cmw_forums_response WHERE forum_topic_id = :forum_topic_id";
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

    public function getResponseById(int $id): ?responseEntity
    {

        $sql = "SELECT * FROM cmw_forums_response WHERE forum_response_id = :response_id ";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("response_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = $this->userModel->getUserById($res["user_id"]);
        $topic = $this->topicModel->getTopicById($res["forum_topic_id"]);

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

    public function countResponseInTopic(int $id): mixed
    {
        $sql = "SELECT COUNT(forum_response_id) FROM cmw_forums_response WHERE forum_topic_id = :forum_topic_id";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_topic_id" => $id))) {
            return 0;
        }

        return $res->fetch(0);
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

}