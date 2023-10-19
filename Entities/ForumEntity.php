<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Utils\Website;

class ForumEntity
{

    private int $forumId;
    private string $forumName;
    private string $forumIcon;
    private string $forumDescription;
    private int $forumIsRestricted;
    private int $disallowTopics;
    private string $forumSlug;
    private string $forumCreated;
    private string $forumUpdated;
    private ForumEntity|ForumCategoryEntity $forumParent;
    private ?array $restrictedRoles;

    public function __construct(int $id, string $name, string $icon, string $desc, int $forumIsRestricted, int $disallowTopics, string $forumSlug, string $forumCreated, string $forumUpdated, ForumEntity|ForumCategoryEntity $parent, ?array $restrictedRoles)
    {
        $this->forumId = $id;
        $this->forumName = $name;
        $this->forumIcon = $icon;
        $this->forumDescription = $desc;
        $this->forumIsRestricted = $forumIsRestricted;
        $this->disallowTopics = $disallowTopics;
        $this->forumSlug = $forumSlug;
        $this->forumCreated = $forumCreated;
        $this->forumUpdated = $forumUpdated;
        $this->forumParent = $parent;
        $this->restrictedRoles = $restrictedRoles;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->forumId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->forumName;
    }

    /**
     * @return string
     */
    public function getFontAwesomeIcon(?string $param = null): string
    {
        return '<i class="' . $this->forumIcon . '  ' . $param . '"></i>';
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->forumIcon;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->forumDescription;
    }

    /**
     * @return bool
     */
    public function isRestricted(): bool
    {
        return $this->forumIsRestricted;
    }

    /**
     * @return bool
     */
    public function isUserAllowed(): bool
    {
        if (!$this->isRestricted()) {
            return true;
        }

        if (!UsersController::isUserLogged() && $this->isRestricted()) {
            return false;
        }

        if ($this->restrictedRoles === null) {
            return true;
        }

        foreach ($this->restrictedRoles as $restrictedRole) {
            if (ForumPermissionRoleModel::playerHasForumRole($restrictedRole?->getId())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function disallowTopics(): bool
    {
        return $this->disallowTopics;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->forumSlug;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->forumCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->forumUpdated);
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        $baseUrl = Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER")."forum/";
        $catSlug = ForumCategoryModel::getInstance()->getCategoryByForumId($this->forumId)->getSlug();
        return $baseUrl."c/$catSlug/f/$this->forumSlug";
    }

    public function getParent(): ForumEntity|ForumCategoryEntity
    {
        return $this->forumParent;
    }

    public function isParentCategory(): bool
    {
        return get_class($this->forumParent) !== get_class($this);
    }

    public function getAdminDeleteLink(): string
    {
        return "./delete/$this->forumId";
    }

    /**
     * @return \CMW\Entity\Forum\ForumResponseEntity|null
     */
    public function getLastResponse(): ?ForumResponseEntity
    {
        return (new ForumResponseModel())->getLatestResponseInForum($this->forumId);
    }


}