<?php

namespace CMW\Entity\Forum;

use CMW\Model\Forum\FeedbackModel;

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
    public function countFeedbackReaction(int $topicId): string
    {
        return feedbackModel::getinstance()->countFeedbackByFeedbackId($topicId,$this->getId());
    }
}