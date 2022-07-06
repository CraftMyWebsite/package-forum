<?php

namespace CMW\Entity\Forums;

use CMW\Entity\Users\userEntity;

class responseEntity
{

    private int $responseId;
    private string $responseContent;
    private topicEntity $responseTopic;
    private userEntity $responseUser;

    public function __construct(int $id, string $content, topicEntity $topic, userEntity $user)
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
     * @return \CMW\Entity\Forums\topicEntity
     */
    public function getResponseTopic(): topicEntity
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
}