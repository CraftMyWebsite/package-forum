<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumReportedResponseEntity;
use CMW\Entity\Forum\ForumReportedTopicEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ForumReportedModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */

class ForumReportedModel extends AbstractModel
{
    /**
     * @return \CMW\Entity\Forum\ForumReportedTopicEntity[]
     */
    public function getTopicsReported(): array
    {

        $sql = "SELECT forums_reported_topic_id FROM cmw_forums_topic_reported";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($topic = $res->fetch()) {
            $toReturn[] = $this->getReportedTopicById($topic["forums_reported_topic_id"]);
        }
        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Forum\ForumReportedResponseEntity[]
     */
    public function getResponsesReported(): array
    {

        $sql = "SELECT forums_reported_response_id FROM cmw_forums_response_reported";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($response = $res->fetch()) {
            $toReturn[] = $this->getReportedResponseById($response["forums_reported_response_id"]);
        }
        return $toReturn;
    }

    public function getReportedTopicById(int $id): ?ForumReportedTopicEntity
    {
        $sql = "SELECT * FROM cmw_forums_topic_reported WHERE forums_reported_topic_id = :forums_reported_topic_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forums_reported_topic_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = UsersModel::getInstance()->getUserById($res["user_id"]);
        $topic = ForumTopicModel::getInstance()->getTopicById($res["forum_reported_topic_id"]);

        return new ForumReportedTopicEntity(
            $res["forums_reported_topic_id"],
            $user,
            $topic,
            $res["forum_reported_topic_reason"],
            $res["forum_reported_updated"]
        );
    }
    public function getReportedResponseById(int $id): ?ForumReportedResponseEntity
    {
        $sql = "SELECT * FROM cmw_forums_response_reported WHERE forums_reported_response_id = :forums_reported_response_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forums_reported_response_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = UsersModel::getInstance()->getUserById($res["user_id"]);
        $response = ForumResponseModel::getInstance()->getResponseById($res["forum_reported_response_id"]);

        return new ForumReportedResponseEntity(
            $res["forums_reported_response_id"],
            $user,
            $response,
            $res["forum_reported_response_reason"],
            $res["forum_reported_updated"]
        );
    }

    public function creatTopicReport(int $userId, int $topicId, int $reason): ?ForumReportedTopicEntity
    {

        $data = array(
            "user_id" => $userId,
            "forum_reported_topic_id" => $topicId,
            "forum_reported_topic_reason" => $reason
        );

        $sql = "INSERT INTO cmw_forums_topic_reported(user_id, forum_reported_topic_id, forum_reported_topic_reason) VALUES (:user_id, :forum_reported_topic_id, :forum_reported_topic_reason)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getReportedTopicById($id);
        }

        return null;
    }

    public function creatResponseReport(int $userId, int $responseId, int $reason): ?ForumReportedResponseEntity
    {

        $data = array(
            "user_id" => $userId,
            "forum_reported_response_id" => $responseId,
            "forum_reported_response_reason" => $reason
        );

        $sql = "INSERT INTO cmw_forums_response_reported(user_id, forum_reported_response_id, forum_reported_response_reason) VALUES (:user_id, :forum_reported_response_id, :forum_reported_response_reason)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getReportedResponseById($id);
        }

        return null;
    }

    public function removeReportTopic(int $id): bool
    {
        $sql = "DELETE FROM cmw_forums_topic_reported WHERE `forums_reported_topic_id` = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if (!$req->execute(array("id" => $id))) {
            return false;
        }
        return $req->rowCount() === 1;
    }

    public function removeReportResponse(int $id): bool
    {
        $sql = "DELETE FROM cmw_forums_response_reported WHERE `forums_reported_response_id` = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if (!$req->execute(array("id" => $id))) {
            return false;
        }
        return $req->rowCount() === 1;
    }


}