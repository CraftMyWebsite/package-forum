<?php

namespace CMW\Entity\Forums;

use CMW\Entity\Users\userEntity;
use CMW\Model\Users\usersModel;

class topicEntity
{

    private int $topicId;
    private string $topicName;
    private string $topicContent;
    private forumEntity $topicForum;
    private userEntity $topicUser;

    public function __construct(int $id, string $name, string $content, forumEntity $forum, userEntity $user)
    {
        $this->topicId = $id;
        $this->topicName = $name;
        $this->topicContent = $content;
        $this->topicForum = $forum;
        $this->topicUser = $user;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->topicId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->topicName;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->topicContent;
    }

    /**
     * @return \CMW\Entity\Forums\forumEntity
     */
    public function getForum(): forumEntity
    {
        return $this->topicForum;
    }

    /**
     * @return \CMW\Entity\Users\userEntity
     */
    public function getUser(): userEntity
    {
        return $this->topicUser;
    }

}