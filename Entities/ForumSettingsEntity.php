<?php

namespace CMW\Entity\Forum;

use CMW\Utils\Date;
use CMW\Model\Forum\ForumSettingsModel;

/**
 * Class: @ForumCategoryModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumSettingsEntity
{
    private int $settingsId;
    private string $settingsName;
    private string $settingsValue;
    private string $settingsUpdate;

    public function __construct(int $id, string $name, string $value, string $update)
    {
        $this->settingsId = $id;
        $this->settingsName = $name;
        $this->settingsValue = $value;
        $this->settingsUpdate = $update;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->settingsId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->settingsName;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->settingsValue;
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return Date::formatDate($this->settingsUpdate);
    }
}
