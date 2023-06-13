<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\FeedbackEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @TopicModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class FeedbackModel extends AbstractModel
{
    /**
     * @return \CMW\Entity\Forum\FeedbackEntity[]
     */
    public function getFeedbacks(): array
    {

        $sql = "SELECT forum_feedback_id FROM cmw_forums_feedback";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($feedback = $res->fetch()) {
            $toReturn[] = $this->getFeedbackById($feedback["forum_feedback_id"]);
        }

        return $toReturn;

    }
    public function getFeedbackById(int $id): ?FeedbackEntity
    {
        $sql = "SELECT * FROM cmw_forums_feedback WHERE forum_feedback_id = :feedbackId";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("feedbackId" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new FeedbackEntity(
            $res["forum_feedback_id"],
            $res["forum_feedback_name"]
        );
    }

    public function addFeedbackByFeedbackId(int $topicId, int $feedbackId, int $userId): ?FeedbackEntity
    {
        $sql = "INSERT INTO cmw_forums_topics_feedback (forum_topics_id, forum_feedback_id, user_id) VALUES (:topicId, :feedbackId, :userId)";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if ($res->execute(array("feedbackId" => $feedbackId, "topicId" => $topicId, "userId" => $userId))) {
            return null;
        }

    }

    /**
     * @param int $topicId
     * @param int $feedbackId
     * @return string
     * @desc count number of feedback by topic id and feedback id
     */
    public function countTopicFeedbackByTopic(int $topicId, int $feedbackId): string
    {
        $sql = "SELECT COUNT(forum_topics_feedback_id) as count FROM cmw_forums_topics_feedback WHERE forum_topics_id = :topic_id AND forum_feedback_id = :feedbackId";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("topic_id" => $topicId, "feedbackId" => $feedbackId))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    /**
     * @param int $userId
     * @return string
     * @desc count number of feedback by topic id and feedback id
     */
    public function countTopicFeedbackByUser(int $userId): string
    {
        $sql = "SELECT COUNT(forum_topics_feedback_id) as count FROM cmw_forums_topics_feedback WHERE user_id = :userId";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("userId" => $userId))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    /**
     * @param int $topicId
     * @param int $userId
     * @return bool
     * @desc user can react to this
     */
    public function userCanReact(int $topicId, ?int $userId): bool
    {
        if ($userId === null){
            return  false;
        }

        $sql = "SELECT forum_topics_feedback_id FROM `cmw_forums_topics_feedback` WHERE forum_topics_id = :topic_id AND user_id = :user_id";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array("topic_id" => $topicId, "user_id" => $userId));

        return count($res->fetchAll()) === 0;
    }

    /**
     * @param int $topicId
     * @param int $userId
     * @return ?FeedbackEntity
     * @desc user can react to this
     */
    public function getFeedbackReactedByUser(int $topicId, int $userId): int
    {
        $sql = "SELECT forum_feedback_id FROM `cmw_forums_topics_feedback` WHERE forum_topics_id = :topic_id AND user_id = :user_id";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array("topic_id" => $topicId, "user_id" => $userId));

        $option = $res->fetch();

        return $option['forum_feedback_id'];
    }
}