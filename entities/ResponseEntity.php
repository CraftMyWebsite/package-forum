<?php

namespace CMW\Entity\Forum;

use CMW\Entity\Users\userEntity;
use CMW\Model\Users\UsersModel;
use CMW\Controller\Core\CoreController;

class ResponseEntity
{

    private int $responseId;
    private string $responseContent;
    private string $responseCreated;
    private string $responseUpdated;
    private TopicEntity $responseTopic;
    private userEntity $responseUser;

    public function __construct(int $id, string $content, string $created, string $updated, TopicEntity $topic, userEntity $user)
    {
        $this->responseId = $id;
        $this->responseContent = $content;
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
    public function deleteLink(): string
    {
        return "{$this->responseTopic->getSlug()}/delete/$this->responseId";
    }

    /**
     * @return bool
     * @des Return true if the response was performe by the topic author
     */
    public function isTopicAuthor(): bool
    {
        return $this->getResponseTopic()->getUser()->getId() === $this->getUser()->getId();
    }
}