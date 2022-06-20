<?php

namespace CMW\Entity\Forums;

use CMW\Model\Users\usersModel;

class topicEntity
{

    private int $topicId;
    private string $topicName;
    private string $topicContent;
    private forumEntity $topicForum;
    private usersModel $topicUser;

    public function __construct(int $id, string $name, string $content, forumEntity $forum, usersModel $user)
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
     * @return \CMW\Model\Users\usersModel
     */
    public function getUser(): usersModel
    {
        return $this->topicUser;
    }

}