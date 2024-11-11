<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\Forum\ForumFeedbackModel;

class ForumFeedbackEntity extends AbstractEntity
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
    public function getImageName(): string
    {
        return $this->feedbackImage;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Forum/' . $this->feedbackImage;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->feedbackName;
    }

    /**
     * @param int $topicId
     * @return string
     */
    public function countTopicFeedbackReceived(int $topicId): string
    {
        return ForumFeedbackModel::getinstance()->countTopicFeedbackByTopic($topicId, $this->getId());
    }

    /**
     * @param int $responseId
     * @return string
     */
    public function countResponseFeedbackReceived(int $responseId): string
    {
        return ForumFeedbackModel::getinstance()->countResponseFeedbackByTopic($responseId, $this->getId());
    }

    /**
     * @param int $topicId
     * @return string
     */
    public function countUserFeedbackReceived(int $topicId): string
    {
        return ForumFeedbackModel::getinstance()->countTopicFeedbackByUser($topicId);
    }

    /**
     * @param int $topicId
     * @return bool
     */
    public function userCanTopicReact(int $topicId): bool
    {
        return !ForumFeedbackModel::getInstance()->userCanTopicReact(
            $topicId,
            UsersSessionsController::getInstance()->getCurrentUser()?->getId()
        );
    }

    /**
     * @param int $topicId
     * @return int
     */
    public function getFeedbackTopicReacted(int $topicId): int
    {
        return ForumFeedbackModel::getInstance()->getFeedbackTopicReactedByUser(
            $topicId,
            UsersSessionsController::getInstance()->getCurrentUser()?->getId(),
        );
    }

    /**
     * @param int $responseId
     * @return bool
     */
    public function userCanResponseReact(int $responseId): bool
    {
        return !(new ForumFeedbackModel())->userCanResponseReact(
            $responseId,
            UsersSessionsController::getInstance()->getCurrentUser()?->getId(),
        );
    }

    /**
     * @param int $responseId
     * @return int
     */
    public function getFeedbackResponseReacted(int $responseId): int
    {
        return ForumFeedbackModel::getInstance()->getFeedbackResponseReactedByUser(
            $responseId,
            UsersSessionsController::getInstance()->getCurrentUser()?->getId(),
        );
    }

    /**
     * @param int $responseId
     * @return int
     */
    public function getUserFeedbackList(int $responseId): int
    {
        return ForumFeedbackModel::getInstance()->getFeedbackResponseReactedByUser(
            $responseId,
            UsersSessionsController::getInstance()->getCurrentUser()?->getId(),
        );
    }
}
