<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Core\CoreController;

class ForumPermissionEntity
{
    private int $permissionId;
    private ?int $permissionParentId;
    private string $permissionCode;

    public function __construct(int $permissionId, ?int $permissionParentId, string $permissionCode)
    {
        $this->permissionId = $permissionId;
        $this->permissionParentId = $permissionParentId;
        $this->permissionCode = $permissionCode;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->permissionId;
    }

    /**
     * @return ?int
     */
    public function getParent(): ?int
    {
        return $this->permissionParentId;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->permissionCode;
    }
}
