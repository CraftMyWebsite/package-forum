<?php

namespace CMW\Entity\Forum;

use CMW\Model\Forum\FeedbackModel;
use CMW\Model\Users\UsersModel;

class FeedbackEntity
{
    private int $feedbackId;
    private string $feedbackName;

    public function __construct(int $id, string $name)
    {
        $this->feedbackId = $id;
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
     * @return int
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
        return feedbackModel::getinstance()->countTopicFeedbackByTopic($topicId,$this->getId());
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
    public function userCanReact(int $topicId): bool
    {
        return !(new feedbackModel())->userCanReact($topicId, (new UsersModel())::getCurrentUser()?->getId());
    }

    /**
     * @return int
     */
    public function getFeedbackReacted(int $topicId): int
    {
        return feedbackModel::getInstance()->getFeedbackReactedByUser($topicId,(new UsersModel())::getCurrentUser()?->getId());
    }

}