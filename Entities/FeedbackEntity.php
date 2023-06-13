<?php

namespace CMW\Entity\Forum;

use CMW\Model\Forum\FeedbackModel;
use CMW\Model\Users\UsersModel;

class FeedbackEntity
{
    private int $feedbackId;
    private string $feedbackImage;

    public function __construct(int $id, string $image)
    {
        $this->feedbackId = $id;
        $this->feedbackImage = $image;
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
        return getenv("PATH_SUBFOLDER") . "Public/Uploads/Forum/" . $this->feedbackImage;
    }

    /**
     * @return string
     */
    public function countTopicFeedbackReceived(int $topicId): string
    {
        return feedbackModel::getinstance()->countTopicFeedbackByTopic($topicId,$this->getId());
    }

    /**
     * @return string
     */
    public function countResponseFeedbackReceived(int $responseId): string
    {
        return feedbackModel::getinstance()->countResponseFeedbackByTopic($responseId,$this->getId());
    }

    /**
     * @return string
     */
    public function countUserFeedbackReceived(int $topicId): string
    {
        return feedbackModel::getinstance()->countTopicFeedbackByUser($topicId,$this->getId());
    }

    /**
     * @return bool
     */
    public function userCanTopicReact(int $topicId): bool
    {
        return !(new feedbackModel())->userCanTopicReact($topicId, (new UsersModel())::getCurrentUser()?->getId());
    }

    /**
     * @return int
     */
    public function getFeedbackTopicReacted(int $topicId): int
    {
        return feedbackModel::getInstance()->getFeedbackTopicReactedByUser($topicId,(new UsersModel())::getCurrentUser()?->getId());
    }

    /**
     * @return bool
     */
    public function userCanResponseReact(int $responseId): bool
    {
        return !(new feedbackModel())->userCanResponseReact($responseId, (new UsersModel())::getCurrentUser()?->getId());
    }

    /**
     * @return int
     */
    public function getFeedbackResponseReacted(int $responseId): int
    {
        return feedbackModel::getInstance()->getFeedbackResponseReactedByUser($responseId,(new UsersModel())::getCurrentUser()?->getId());
    }

    /**
     * @return int
     */
    public function getUserFeedbackList(int $responseId): int
    {
        return feedbackModel::getInstance()->getFeedbackResponseReactedByUser($responseId,(new UsersModel())::getCurrentUser()?->getId());
    }

}