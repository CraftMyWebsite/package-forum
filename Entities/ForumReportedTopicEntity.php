<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserEntity;

class ForumReportedTopicEntity
{
    private int $id;
    private userEntity $user;
    private ForumTopicEntity $topic;
    private int $reason;
    private string $update;

    /**
     * @param int $id
     * @param \CMW\Entity\Users\userEntity $user
     * @param \CMW\Entity\Forum\ForumTopicEntity $topic
     * @param int $reason
     * @param string update
     */
    public function __construct(int $id, userEntity $user, ForumTopicEntity $topic, int $reason, string $update)
    {
        $this->id = $id;
        $this->user = $user;
        $this->topic = $topic;
        $this->reason = $reason;
        $this->update = $update;
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
     * @return \CMW\Entity\Forum\ForumTopicEntity
     */
    public function getTopic(): ForumTopicEntity
    {
        return $this->topic;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        if ($this->reason === 0) {
            return "Autre";
        }
        if ($this->reason === 1) {
            return "Nom du topic inapproprié";
        }
        if ($this->reason === 2) {
            return "Le topic n'est pas au bon endroit";
        }
        if ($this->reason === 3) {
            return "Contenue choquant";
        }
        if ($this->reason === 4) {
            return "Harcèlement, discrimination ...";
        }

        return $this->reason;
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->update);
    }
}