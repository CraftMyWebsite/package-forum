<?php

namespace CMW\Entity\Forum;

use CMW\Manager\Package\AbstractEntity;

class ForumPermissionRoleEntity extends AbstractEntity
{
    private int $forumRoleId;
    private string $forumRoleName;
    private string $forumRoleDescription;
    private int $forumRoleWeight;
    private bool $forumRoleIsDefault;
    private array $forumRolePermissions;

    /**
     * @param int $forumRoleId
     * @param string $forumRoleName
     * @param string $forumRoleDescription
     * @param int $forumRoleWeight
     * @param bool $forumRoleIsDefault
     * @param ForumPermissionEntity[] $forumRolePermissions
     */
    public function __construct(int $forumRoleId, string $forumRoleName, string $forumRoleDescription, int $forumRoleWeight, bool $forumRoleIsDefault, array $forumRolePermissions)
    {
        $this->forumRoleId = $forumRoleId;
        $this->forumRoleName = $forumRoleName;
        $this->forumRoleDescription = $forumRoleDescription;
        $this->forumRoleWeight = $forumRoleWeight;
        $this->forumRoleIsDefault = $forumRoleIsDefault;
        $this->forumRolePermissions = $forumRolePermissions;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->forumRoleId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->forumRoleName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->forumRoleDescription;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->forumRoleWeight;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->forumRoleIsDefault;
    }

    /**
     * @return ForumPermissionEntity[]
     */
    public function getPermissions(): array
    {
        return $this->forumRolePermissions;
    }
}
