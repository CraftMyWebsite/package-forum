<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumEntity;
use CMW\Entity\Forum\ForumResponseEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ForumResponseModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumResponseModel extends AbstractModel
{
    /** @var \CMW\Model\Users\UsersModel */
    private UsersModel $userModel;
    /** @var \CMW\Model\Forum\ForumTopicModel */
    private ForumTopicModel $topicModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->topicModel = new ForumTopicModel();
    }

    /**
     * @return \CMW\Entity\Forum\ForumResponseEntity[]
     */
    public function getResponseByTopicAndOffset(int $id, int $offset, int $responsePerPage): array
    {
        $sql = 'SELECT forum_response_id FROM cmw_forums_response WHERE forum_topic_id = :forum_topic_id AND forum_response_is_trash = 0 LIMIT :responsePerPage OFFSET :offset';
        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array('forum_topic_id' => $id, 'offset' => $offset, 'responsePerPage' => $responsePerPage))) {
            return array();
        }

        $toReturn = array();

        while ($resp = $res->fetch()) {
            $toReturn[] = $this->getResponseById($resp['forum_response_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Forum\ForumResponseEntity|null
     */
    public function getResponseById(int $id): ?ForumResponseEntity
    {
        $sql = 'SELECT * FROM cmw_forums_response WHERE forum_response_id = :response_id ';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('response_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = $this->userModel->getUserById($res['user_id']);
        $topic = $this->topicModel->getTopicById($res['forum_topic_id']);

        if (is_null($topic) || is_null($user?->getPseudo())) {
            return null;
        }

        return new ForumResponseEntity(
            $res['forum_response_id'],
            $res['forum_response_content'],
            $res['forum_response_is_trash'],
            $res['forum_response_trash_reason'],
            $res['forum_response_created'],
            $res['forum_response_updated'],
            $topic,
            $user
        );
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function countResponseInTopic(int $id): mixed
    {
        $sql = 'SELECT COUNT(forum_response_id) as count FROM cmw_forums_response WHERE forum_topic_id = :forum_topic_id AND forum_response_is_trash = 0';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('forum_topic_id' => $id))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    /**
     * @param int $topicId
     * @param int $responseId
     * @param int $responsePerPage
     * @return int
     */
    public function getResponsePageNumber(int $topicId, int $responseId, int $responsePerPage): int
    {
        $sql = 'SELECT*,FLOOR((position - 1) / :responsePerPage) + 1 AS page FROM (SELECT forum_response_id, ROW_NUMBER() OVER (ORDER BY forum_response_id) AS position FROM cmw_forums_response WHERE forum_topic_id = :topicId AND forum_response_is_trash = 0) AS response WHERE response.forum_response_id = :responseId';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('topicId' => $topicId, 'responseId' => $responseId, 'responsePerPage' => $responsePerPage))) {
            return 0;
        }

        return $res->fetch(0)['page'];
    }

    /**
     * @param int $topicId
     * @param int $responseId
     * @param int $responsePerPage
     * @return int
     */
    public function getResponsePosition(int $topicId, int $responseId, int $responsePerPage): int
    {
        $sql = 'SELECT*,FLOOR((position - 1) / :responsePerPage) + 1 AS page FROM (SELECT forum_response_id, ROW_NUMBER() OVER (ORDER BY forum_response_id) AS position FROM cmw_forums_response WHERE forum_topic_id = :topicId AND forum_response_is_trash = 0) AS response WHERE response.forum_response_id = :responseId';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('topicId' => $topicId, 'responseId' => $responseId, 'responsePerPage' => $responsePerPage))) {
            return 0;
        }

        return $res->fetch(0)['position'];
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function countResponseInTopicWithoutTrashFunction(int $id): mixed  // never use this model without knowing what it really does!!
    {
        $sql = 'SELECT COUNT(forum_response_id) as count FROM cmw_forums_response WHERE forum_topic_id = :forum_topic_id';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('forum_topic_id' => $id))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function countResponseByUser(int $id): mixed
    {
        $sql = 'SELECT COUNT(forum_response_id) as count FROM cmw_forums_response WHERE user_id = :user_id AND forum_response_is_trash = 0';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('user_id' => $id))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    /**
     * @param string $content
     * @param int $userId
     * @param int $topicId
     * @return \CMW\Entity\Forum\ForumResponseEntity|null
     */
    public function createResponse(string $content, int $userId, int $topicId): ?ForumResponseEntity
    {
        $var = array(
            'response_content' => $content,
            'user_id' => $userId,
            'topic_id' => $topicId
        );

        $sql = 'INSERT INTO cmw_forums_response(forum_response_content, forum_topic_id, user_id) VALUES (:response_content, :topic_id, :user_id)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $this->getResponseById($db->lastInsertId());
        }

        return null;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteResponse(int $id): bool
    {
        $sql = 'DELETE FROM cmw_forums_response WHERE forum_response_id = :id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array('id' => $id))) {
            return false;
        }

        return $req->rowCount() === 1;
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Forum\ForumResponseEntity|null
     */
    public function restoreResponse(int $id): ?ForumResponseEntity
    {
        $sql = "UPDATE `cmw_forums_response` SET `forum_response_is_trash` = '0' WHERE `forum_response_id` = :id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute(array('id' => $id))) {
            return $this->getResponseById($id);
        }

        return null;
    }

    /**
     * @param int $id
     * @param int $reason
     * @return \CMW\Entity\Forum\ForumResponseEntity|null
     */
    public function trashResponse(int $id, int $reason): ?ForumResponseEntity
    {
        $sql = "UPDATE `cmw_forums_response` SET `forum_response_is_trash` = '1', `forum_response_trash_reason` = :reason WHERE `forum_response_id` = :id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute(array('id' => $id, 'reason' => $reason))) {
            return $this->getResponseById($id);
        }

        return null;
    }

    /**
     * @return array
     */
    public function getTrashResponse(): array
    {
        $sql = 'SELECT * FROM `cmw_forums_response` WHERE `forum_response_is_trash` = 1 ORDER BY `cmw_forums_response`.`forum_response_updated` DESC';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($response = $res->fetch()) {
            $toReturn[] = $this->getResponseById($response['forum_response_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $topicId
     * @return \CMW\Entity\Forum\ForumResponseEntity|null
     */
    public function getLatestResponseInTopic(int $topicId): ?ForumResponseEntity
    {
        $sql = 'SELECT * FROM `cmw_forums_response` 
                                           WHERE `forum_topic_id` = :forum_topic_id AND forum_response_is_trash = 0
                                           ORDER BY `cmw_forums_response`.`forum_response_id` 
                                           DESC limit 1 offset 0';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array('forum_topic_id' => $topicId))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $user = $this->userModel->getUserById($res['user_id']);
        $topic = $this->topicModel->getTopicById($res['forum_topic_id']);

        if (is_null($topic) || is_null($user?->getPseudo())) {
            return null;
        }

        return new ForumResponseEntity(
            $res['forum_response_id'],
            $res['forum_response_content'],
            $res['forum_response_is_trash'],
            $res['forum_response_trash_reason'],
            $res['forum_response_created'],
            $res['forum_response_updated'],
            $topic,
            $user
        );
    }

    /**
     * @param int $forumId
     * @return \CMW\Entity\Forum\ForumResponseEntity|null
     */
    public function getLatestResponseInForum(int $forumId): ?ForumResponseEntity
    {
        $sql = 'WITH RECURSIVE ForumHierarchy AS (
  SELECT forum_id, forum_subforum_id, forum_id AS root_forum_id
  FROM cmw_forums
  WHERE forum_id = :forum_id
  
  UNION ALL
  
  SELECT f2.forum_id, f2.forum_subforum_id, fh.root_forum_id
  FROM cmw_forums f2
  INNER JOIN ForumHierarchy fh ON f2.forum_subforum_id = fh.forum_id
)

SELECT r.*
FROM ForumHierarchy fh
LEFT JOIN cmw_forums_topics t ON fh.forum_id = t.forum_id
LEFT JOIN cmw_forums_response r ON t.forum_topic_id = r.forum_topic_id
WHERE r.forum_response_created = (
  SELECT MAX(forum_response_created)
  FROM cmw_forums_response
  WHERE forum_topic_id = t.forum_topic_id
) AND r.forum_response_is_trash = 0 ORDER BY r.forum_response_id DESC LIMIT 1;';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['forum_id' => $forumId])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        $user = $this->userModel->getUserById($res['user_id']);
        $topic = $this->topicModel->getTopicById($res['forum_topic_id']);

        if (is_null($topic) || is_null($user?->getPseudo())) {
            return null;
        }

        return new ForumResponseEntity(
            $res['forum_response_id'],
            $res['forum_response_content'],
            $res['forum_response_is_trash'],
            $res['forum_response_trash_reason'],
            $res['forum_response_created'],
            $res['forum_response_updated'],
            $topic,
            $user
        );
    }
}
