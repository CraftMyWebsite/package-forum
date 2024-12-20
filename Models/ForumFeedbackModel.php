<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumFeedbackEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Uploads\ImagesManager;

/**
 * Class: @ForumTopicModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumFeedbackModel extends AbstractModel
{
    /**
     * @return \CMW\Entity\Forum\ForumFeedbackEntity[]
     */
    public function getFeedbacks(): array
    {
        $sql = 'SELECT forum_feedback_id FROM cmw_forums_feedback';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($feedback = $res->fetch()) {
            $toReturn[] = $this->getFeedbackById($feedback['forum_feedback_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Forum\ForumFeedbackEntity|null
     */
    public function getFeedbackById(int $id): ?ForumFeedbackEntity
    {
        $sql = 'SELECT * FROM cmw_forums_feedback WHERE forum_feedback_id = :feedbackId';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('feedbackId' => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ForumFeedbackEntity(
            $res['forum_feedback_id'],
            $res['forum_feedback_image'],
            $res['forum_feedback_name']
        );
    }

    /**
     * @return array
     */
    public function getTopicUsersFeedbackByFeedbackId(int $topicId, int $feedbackId): array
    {
        $sql = 'SELECT user_id FROM cmw_forums_topics_feedback WHERE forum_topics_id = :topicId AND forum_feedback_id = :feedbackId';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req) {
            return [];
        }

        if (!$req->execute(array('topicId' => $topicId, 'feedbackId' => $feedbackId))) {
            return array();
        }

        $toReturn = array();

        while ($user = $req->fetch()) {
            $toReturn[] = $user['user_id'];
        }

        return $toReturn;
    }

    /**
     * @return array
     */
    public function getResponseUsersFeedbackByFeedbackId(int $responseId, int $feedbackId): array
    {
        $sql = 'SELECT user_id FROM cmw_forums_response_feedback WHERE forum_response_id = :responseId AND forum_feedback_id = :feedbackId';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req) {
            return [];
        }

        if (!$req->execute(array('responseId' => $responseId, 'feedbackId' => $feedbackId))) {
            return array();
        }

        $toReturn = array();

        while ($user = $req->fetch()) {
            $toReturn[] = $user['user_id'];
        }

        return $toReturn;
    }

    /**
     * @param array $image
     * @param string $name
     * @return \CMW\Entity\Forum\ForumFeedbackEntity|null
     */
    public function createFeedback(array $image, string $name): ?ForumFeedbackEntity
    {
        $imageName = ImagesManager::convertAndUpload($image, 'Forum');
        $data = array(
            'image' => $imageName,
            'name' => $name
        );

        $sql = 'INSERT INTO cmw_forums_feedback(forum_feedback_image, forum_feedback_name) VALUES (:image, :name)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getFeedbackById($id);
        }

        return null;
    }

    /**
     * @param array $image
     * @param string $name
     * @param int $id
     * @return \CMW\Entity\Forum\ForumFeedbackEntity|null
     * @throws \JsonException
     */
    public function editFeedback(array $image, string $name, int $id): ?ForumFeedbackEntity
    {
        $imageName = ImagesManager::convertAndUpload($image, 'Forum');
        $data = array(
            'image' => $imageName,
            'name' => $name,
            'id' => $id
        );

        $sql = 'UPDATE cmw_forums_feedback SET forum_feedback_name = :name, forum_feedback_image = :image WHERE forum_feedback_id = :id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getFeedbackById($id);
        }

        return null;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function removeFeedback(int $id): bool
    {
        $sql = 'DELETE FROM cmw_forums_feedback WHERE `forum_feedback_id` = :id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if (!$req->execute(array('id' => $id))) {
            return false;
        }
        return $req->rowCount() === 1;
    }

    /**
     * @param int $topicId
     * @param int $feedbackId
     * @param int $userId
     * @return \CMW\Entity\Forum\ForumFeedbackEntity|null
     */
    public function addFeedbackByFeedbackId(int $topicId, int $feedbackId, int $userId): ?ForumFeedbackEntity
    {
        $sql = 'INSERT INTO cmw_forums_topics_feedback (forum_topics_id, forum_feedback_id, user_id) VALUES (:topicId, :feedbackId, :userId)';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if ($res->execute(array('feedbackId' => $feedbackId, 'topicId' => $topicId, 'userId' => $userId))) {
            return null;
        }
    }

    /**
     * @param int $topicId
     * @param int $feedbackId
     * @param int $userId
     * @return \CMW\Entity\Forum\ForumFeedbackEntity|null
     */
    public function removeFeedbackByFeedbackId(int $topicId, int $feedbackId, int $userId): ?ForumFeedbackEntity
    {
        $sql = 'DELETE FROM cmw_forums_topics_feedback WHERE `forum_topics_id` = :topicId AND `forum_feedback_id` = :feedbackId AND `user_id` = :userId';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if ($res->execute(array('feedbackId' => $feedbackId, 'topicId' => $topicId, 'userId' => $userId))) {
            return null;
        }
    }

    /**
     * @param int $topicId
     * @param int $feedbackId
     * @param int $userId
     * @return \CMW\Entity\Forum\ForumFeedbackEntity|null
     */
    public function changeFeedbackByFeedbackId(int $topicId, int $feedbackId, int $userId): ?ForumFeedbackEntity
    {
        $sql = 'UPDATE cmw_forums_topics_feedback SET forum_feedback_id = :feedbackId WHERE forum_topics_id = :topicId AND user_id = :userId';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if ($res->execute(array('feedbackId' => $feedbackId, 'topicId' => $topicId, 'userId' => $userId))) {
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
        $sql = 'SELECT COUNT(forum_topics_feedback_id) as count FROM cmw_forums_topics_feedback WHERE forum_topics_id = :topic_id AND forum_feedback_id = :feedbackId';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('topic_id' => $topicId, 'feedbackId' => $feedbackId))) {
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
        $sql = 'SELECT COUNT(forum_topics_feedback_id) as count FROM cmw_forums_topics_feedback WHERE user_id = :userId';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('userId' => $userId))) {
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
    public function userCanTopicReact(int $topicId, ?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }

        $sql = 'SELECT forum_topics_feedback_id FROM `cmw_forums_topics_feedback` WHERE forum_topics_id = :topic_id AND user_id = :user_id';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array('topic_id' => $topicId, 'user_id' => $userId));

        return count($res->fetchAll()) === 0;
    }

    /**
     * @param int $topicId
     * @param int $userId
     * @return ?ForumFeedbackEntity
     * @desc user can react to this
     */
    public function getFeedbackTopicReactedByUser(int $topicId, int $userId): int
    {
        $sql = 'SELECT forum_feedback_id FROM `cmw_forums_topics_feedback` WHERE forum_topics_id = :topic_id AND user_id = :user_id';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array('topic_id' => $topicId, 'user_id' => $userId));

        $option = $res->fetch();

        return $option['forum_feedback_id'];
    }

    /**
     * @param int $responseId
     * @param int $feedbackId
     * @param int $userId
     * @return \CMW\Entity\Forum\ForumFeedbackEntity|null
     */
    public function addFeedbackResponseByFeedbackId(int $responseId, int $feedbackId, int $userId): ?ForumFeedbackEntity
    {
        $sql = 'INSERT INTO cmw_forums_response_feedback (forum_response_id, forum_feedback_id, user_id) VALUES (:ResponseId, :feedbackId, :userId)';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if ($res->execute(array('feedbackId' => $feedbackId, 'ResponseId' => $responseId, 'userId' => $userId))) {
            return null;
        }
    }

    /**
     * @param int $responseId
     * @param int $feedbackId
     * @param int $userId
     * @return \CMW\Entity\Forum\ForumFeedbackEntity|null
     */
    public function removeFeedbackResponseByFeedbackId(int $responseId, int $feedbackId, int $userId): ?ForumFeedbackEntity
    {
        $sql = 'DELETE FROM cmw_forums_response_feedback WHERE `forum_response_id` = :ResponseId AND `forum_feedback_id` = :feedbackId AND `user_id` = :userId';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if ($res->execute(array('feedbackId' => $feedbackId, 'ResponseId' => $responseId, 'userId' => $userId))) {
            return null;
        }
    }

    /**
     * @param int $responseId
     * @param int $feedbackId
     * @param int $userId
     * @return \CMW\Entity\Forum\ForumFeedbackEntity|null
     */
    public function changeFeedbackResponseByFeedbackId(int $responseId, int $feedbackId, int $userId): ?ForumFeedbackEntity
    {
        $sql = 'UPDATE cmw_forums_response_feedback SET forum_feedback_id = :feedbackId WHERE forum_response_id = :ResponseId AND user_id = :userId';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if ($res->execute(array('feedbackId' => $feedbackId, 'ResponseId' => $responseId, 'userId' => $userId))) {
            return null;
        }
    }

    /**
     * @param int $responseId
     * @param int $feedbackId
     * @return string
     * @desc count number of feedback by topic id and feedback id
     */
    public function countResponseFeedbackByTopic(int $responseId, int $feedbackId): string
    {
        $sql = 'SELECT COUNT(forum_response_feedback_id) as count FROM cmw_forums_response_feedback WHERE forum_response_id = :response_id AND forum_feedback_id = :feedbackId';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('response_id' => $responseId, 'feedbackId' => $feedbackId))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    /**
     * @param int $responseId
     * @param int $userId
     * @return bool
     * @desc user can react to this
     */
    public function userCanResponseReact(int $responseId, ?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }

        $sql = 'SELECT forum_response_feedback_id FROM `cmw_forums_response_feedback` WHERE forum_response_id = :response_id AND user_id = :user_id';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array('response_id' => $responseId, 'user_id' => $userId));

        return count($res->fetchAll()) === 0;
    }

    /**
     * @param int $topicId
     * @param int $userId
     * @return ?ForumFeedbackEntity
     * @desc user can react to this
     */
    public function getFeedbackResponseReactedByUser(int $responseId, int $userId): int
    {
        $sql = 'SELECT forum_feedback_id FROM `cmw_forums_response_feedback` WHERE forum_response_id = :response_id AND user_id = :user_id';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array('response_id' => $responseId, 'user_id' => $userId));

        $option = $res->fetch();

        return $option['forum_feedback_id'];
    }
}
