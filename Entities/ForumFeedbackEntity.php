<?php

namespace CMW\Entity\Forum;

use CMW\Manager\Env\EnvManager;
use CMW\Model\Forum\ForumFeedbackModel;
use CMW\Model\Users\UsersModel;

class ForumFeedbackEntity
{
    private int $feedbackId;
    private string $feedbackImage;
    private string $feedbackName;

    public function __construct(int $id, string $image, string $name)
    {
        $this->feedbackId = $id;
        $this->feedbackImage = $image;
        $this->feedbackName = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->feedbackId;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/Forum/" . $this->feedbackImage;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->feedbackName;
    }

    /**
     * @return string
     */
    public function countTopicFeedbackReceived(int $topicId): string
    {
        return ForumFeedbackModel::getinstance()->countTopicFeedbackByTopic($topicId,$this->getId());
    }

    /**
     * @return string
     */
    public function countResponseFeedbackReceived(int $responseId): string
    {
        return ForumFeedbackModel::getinstance()->countResponseFeedbackByTopic($responseId,$this->getId());
    }

    /**
     * @return string
     */
    public function countUserFeedbackReceived(int $topicId): string
    {
        return ForumFeedbackModel::getinstance()->countTopicFeedbackByUser($topicId,$this->getId());
    }

    /**
     * @return bool
     */
    public function userCanTopicReact(int $topicId): bool
    {
        return !(new ForumFeedbackModel())->userCanTopicReact($topicId, (new UsersModel())::getCurrentUser()?->getId());
    }

    /**
     * @return int
     */
    public function getFeedbackTopicReacted(int $topicId): int
    {
        return ForumFeedbackModel::getInstance()->getFeedbackTopicReactedByUser($topicId,(new UsersModel())::getCurrentUser()?->getId());
    }

    /**
     * @return bool
     */
    public function userCanResponseReact(int $responseId): bool
    {
        return !(new ForumFeedbackModel())->userCanResponseReact($responseId, (new UsersModel())::getCurrentUser()?->getId());
    }

    /**
     * @return int
     */
    public function getFeedbackResponseReacted(int $responseId): int
    {
        return ForumFeedbackModel::getInstance()->getFeedbackResponseReactedByUser($responseId,(new UsersModel())::getCurrentUser()?->getId());
    }

    /**
     * @return int
     */
    public function getUserFeedbackList(int $responseId): int
    {
        return ForumFeedbackModel::getInstance()->getFeedbackResponseReactedByUser($responseId,(new UsersModel())::getCurrentUser()?->getId());
    }

}