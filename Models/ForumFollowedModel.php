<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumFollowedEntity;
use CMW\Entity\Forum\ForumUserBlockedEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ForumFollowedModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */

class ForumFollowedModel extends AbstractModel
{
    /**
     * @return \CMW\Entity\Forum\ForumFollowedEntity[]
     */
    public function getFollowerByTopicId(int $topicId): array
    {
        $sql = "SELECT forums_followed_id FROM cmw_forums_followed WHERE forum_topic_id =:forum_topic_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_topic_id" => $topicId))) {
            return array();
        }

        $toReturn = array();

        while ($topic = $res->fetch()) {
            $toReturn[] = $this->getFollowerById($topic["forums_followed_id"]);
        }
        return $toReturn;
    }

    public function getFollowerById(int $id): ?ForumFollowedEntity
    {
        $sql = "SELECT * FROM cmw_forums_followed WHERE forums_followed_id = :forums_followed_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forums_followed_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = UsersModel::getInstance()->getUserById($res["user_id"]);
        $topic = ForumTopicModel::getInstance()->getTopicById($res["forum_topic_id"]);

        return new ForumFollowedEntity(
            $res["forums_followed_id"],
            $user,
            $topic
        );
    }

    public function addFollower(int $topicId, int $userId): void
    {
        $data = array(
            "forum_topic_id" => $topicId,
            "user_id" => $userId
        );

        $sql = "INSERT INTO cmw_forums_followed(forum_topic_id, user_id) VALUES (:forum_topic_id, :user_id)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);

    }

    public function removeFollower(int $topicId, int $userId): bool
    {
        $sql = "DELETE FROM cmw_forums_followed WHERE forum_topic_id = :forum_topic_id AND user_id = :user_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array("forum_topic_id" => $topicId, "user_id" => $userId));
    }

    public function isFollower(int $topicId, int $userId): bool
    {
        $sql = "SELECT forums_followed_id FROM cmw_forums_followed WHERE forum_topic_id = :forum_topic_id AND user_id = :user_id";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if ($res->execute(array("forum_topic_id" => $topicId, "user_id" => $userId))) {
            return $res->rowCount() === 1;
        }
        return false;
    }

}