<?php

namespace CMW\Entity\Forum;

use CMW\Entity\Users\userEntity;
use CMW\Model\Users\UsersModel;

class ResponseEntity
{

    private int $responseId;
    private string $responseContent;
    private TopicEntity $responseTopic;
    private userEntity $responseUser;

    public function __construct(int $id, string $content, TopicEntity $topic, userEntity $user)
    {
        $this->responseId = $id;
        $this->responseContent = $content;
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
}