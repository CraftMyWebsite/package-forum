<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Users\userEntity;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Utils\Date;

class ForumResponseEntity extends AbstractEntity
{
    private int $responseId;
    private string $responseContent;
    private string $responseIsTrash;
    private string $responseTrashReason;
    private string $responseCreated;
    private string $responseUpdated;
    private ForumTopicEntity $responseTopic;
    private userEntity $responseUser;

    public function __construct(int $id, string $content, int $isTrash, int $trashReason, string $created, string $updated, ForumTopicEntity $topic, userEntity $user)
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
            return 'Topic en corbeille'; // TODO Translate
        }
        if ($this->responseTrashReason == 1) {
            return "Suppression par l'auteur"; // TODO Translate
        }
        if ($this->responseTrashReason == 2) {
            return 'Supprimer par un staff'; // TODO Translate
        }

        //TODO Return something ???
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return Date::formatDate($this->responseCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return Date::formatDate($this->responseUpdated);
    }

    /**
     * @return ForumTopicEntity
     */
    public function getResponseTopic(): ForumTopicEntity
    {
        return $this->responseTopic;
    }

    /**
     * @return userEntity
     */
    public function getUser(): userEntity
    {
        return $this->responseUser;
    }

    public function isSelfReply(): bool
    {
        return $this->getUser()->getId() === UsersSessionsController::getInstance()->getCurrentUser()?->getId();
    }

    /**
     * @return string
     */
    public function trashLink(): string
    {
        if ($this->getUser()->getId() === UsersSessionsController::getInstance()->getCurrentUser()?->getId()) {
            return "p1/trash/$this->responseId/1";
        }

        return "p1/trash/$this->responseId/2";
    }

    /**
     * @return bool
     */
    public function isTopicAuthor(): bool
    {
        return $this->getResponseTopic()->getUser()->getId() === $this->getUser()->getId();
    }

    /**
     * @param int $feedbackId
     * @return string
     */
    public function getFeedbackAddResponseLink(int $feedbackId): string
    {
        return "p1/response_react/$this->responseId/$feedbackId";
    }

    /**
     * @param int $feedbackId
     * @return string
     */
    public function getFeedbackDeleteResponseLink(int $feedbackId): string
    {
        return "p1/response_un_react/$this->responseId/$feedbackId";
    }

    /**
     * @param int $feedbackId
     * @return string
     */
    public function getFeedbackChangeResponseLink(int $feedbackId): string
    {
        return "p1/response_change_react/$this->responseId/$feedbackId";
    }

    /**
     * @return string
     */
    public function getReportLink(): string
    {
        return "p1/reportResponse/$this->responseId";
    }

    /**
     * @return int
     */
    public function getPageNumber(): int
    {
        $topic = $this->getResponseTopic()->getId();
        $response = $this->getId();
        $responsePerPage = ForumSettingsModel::getInstance()->getOptionValue('responsePerPage');
        return ForumResponseModel::getInstance()->getResponsePageNumber($topic, $response, $responsePerPage);
    }

    /**
     * @return int
     */
    public function getResponsePosition(): int
    {
        $topic = $this->getResponseTopic()->getId();
        $response = $this->getId();
        $responsePerPage = ForumSettingsModel::getInstance()->getOptionValue('responsePerPage');
        return ForumResponseModel::getInstance()->getResponsePosition($topic, $response, $responsePerPage);
    }
}
