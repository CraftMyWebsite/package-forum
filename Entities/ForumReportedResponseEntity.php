<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserEntity;

class ForumReportedResponseEntity
{
    private int $id;
    private userEntity $user;
    private ForumResponseEntity $response;
    private int $reason;
    private string $update;

    /**
     * @param int $id
     * @param \CMW\Entity\Users\userEntity $user
     * @param \CMW\Entity\Forum\ForumResponseEntity $response
     * @param int $reason
     * @param string update
     */
    public function __construct(int $id, userEntity $user, ForumResponseEntity $response, int $reason, string $update)
    {
        $this->id = $id;
        $this->user = $user;
        $this->response = $response;
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
     * @return \CMW\Entity\Forum\ForumResponseEntity
     */
    public function getResponse(): ForumResponseEntity
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        if ($this->reason === 0) {
            return 'Autre';
        }
        if ($this->reason === 1) {
            return 'Réponse inapproprié';
        }
        if ($this->reason === 2) {
            return 'Contenue choquant';
        }
        if ($this->reason === 3) {
            return 'Harcèlement, discrimination ...';
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
