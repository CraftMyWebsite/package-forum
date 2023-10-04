<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserEntity;

class ForumUserBlockedEntity
{
    private int $id;
    private userEntity $user;
    private int $isBlocked;
    private string $reason;
    private string $update;

    /**
     * @param int $id
     * @param \CMW\Entity\Users\userEntity $topicUser
     * @param int $isBlocked
     * @param string $reason
     * @param string $update
     */
    public function __construct(int $id, userEntity $user, int $isBlocked, string $reason, string $update)
    {
        $this->id = $id;
        $this->user = $user;
        $this->isBlocked = $isBlocked;
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
     * @return \CMW\Entity\Users\userEntity
     */
    public function getUser(): userEntity
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        if ($this->reason === NULL) {
            return "Cet utilisateur n'a jamais été bloqué";
        } else {
            return $this->reason;
        }
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->update);
    }
}