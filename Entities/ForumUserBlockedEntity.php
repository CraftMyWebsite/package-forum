<?php

namespace CMW\Entity\Forum;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;
use CMW\Entity\Users\UserEntity;

class ForumUserBlockedEntity extends AbstractEntity
{
    private int $id;
    private userEntity $user;
    private int $isBlocked;
    private string $reason;
    private string $update;

    /**
     * @param int $id
     * @param UserEntity $user
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
     * @return UserEntity
     */
    public function getUser(): userEntity
    {
        return $this->user;
    }

    /**
     * @return bool
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
        return $this->reason ?? "Cet utilisateur n'a jamais été bloqué"; //TODO Translate
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return Date::formatDate($this->update);
    }
}
