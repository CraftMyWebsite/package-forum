<?php

namespace CMW\Entity\Forum;

use CMW\Entity\Users\userEntity;
use CMW\Model\Users\UsersModel;
use CMW\Controller\Core\CoreController;

class ResponseEntity
{

    private int $responseId;
    private string $responseContent;
    private string $responseIsTrash;
    private string $responseTrashReason;
    private string $responseCreated;
    private string $responseUpdated;
    private TopicEntity $responseTopic;
    private userEntity $responseUser;

    public function __construct(int $id, string $content, int $isTrash, int $trashReason, string $created, string $updated, TopicEntity $topic, userEntity $user)
    {
        $this->responseId = $id;
        $this->responseContent = $content;
        $this->responseIsTrash = $isTrash;
        $this->responseTrashReason = $trashReason;
        $this->responseCreated = $created;
        $this->responseUpdated = $updated;
        $this->responseTopic = $topic;
        $this->responseUser = $user;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->responseId;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->responseContent;
    }

    /**
     * @return string
     */
    public function getIsTrash(): string
    {
        return $this->responseIsTrash;
    }

    /**
     * @return string
     */
    public function getTrashReason(): string
    {
        if ($this->responseTrashReason == 0) {
            return "Topic en corbeille";
        }
        if ($this->responseTrashReason == 1) {
            return "Auteur";
        }
        if ($this->responseTrashReason == 2) {
            return "Staff";
        }
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->responseCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->responseUpdated);
    }

    /**
     * @return \CMW\Entity\Forum\TopicEntity
     */
    public function getResponseTopic(): TopicEntity
    {
        return $this->responseTopic;
    }

    /**
     * @return \CMW\Entity\Users\userEntity
     */
    public function getUser(): userEntity
    {
        return $this->responseUser;
    }

    public function isSelfReply(): bool
    {
        return $this->getUser()->getId() === UsersModel::getLoggedUser();
    }

    /**
     * @return string
     */
    public function trashLink(): string
    {
        if ($this->getUser()->getId() === UsersModel::getLoggedUser()) {
            return "{$this->responseTopic->getSlug()}/trash/$this->responseId/1";
        } else {
            return "{$this->responseTopic->getSlug()}/trash/$this->responseId/2";
        }
        
    }

    /**
     * @return bool
     */
    public function isTopicAuthor(): bool
    {
        return $this->getResponseTopic()->getUser()->getId() === $this->getUser()->getId();
    }

    /**
     * @return string
     */
    public function getFeedbackAddResponseLink(int $feedbackId): string
    {
        return "{$this->responseTopic->getSlug()}/response_react/$this->responseId/$feedbackId";
    }

    /**
     * @return string
     */
    public function getFeedbackDeleteResponseLink(int $feedbackId): string
    {
        return "{$this->responseTopic->getSlug()}/response_un_react/$this->responseId/$feedbackId";
    }

    /**
     * @return string
     */
    public function getFeedbackChangeResponseLink(int $feedbackId): string
    {
        return "{$this->responseTopic->getSlug()}/response_change_react/$this->responseId/$feedbackId";
    }
}