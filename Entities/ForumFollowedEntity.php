<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Package\AbstractEntity;

class ForumFollowedEntity extends AbstractEntity
{
    private int $id;
    private userEntity $user;
    private ForumTopicEntity $topic;

    /**
     * @param int $id
     * @param \CMW\Entity\Users\userEntity $user
     * @param ForumTopicEntity $topic
     */
    public function __construct(int $id, userEntity $user, ForumTopicEntity $topic)
    {
        $this->id = $id;
        $this->user = $user;
        $this->topic = $topic;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->user;
    }

    /**
     * @return ForumTopicEntity
     */
    public function getTopic(): ForumTopicEntity
    {
        return $this->topic;
    }
}
