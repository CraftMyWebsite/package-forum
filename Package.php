<?php

namespace CMW\Package\Forum;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return 'Forum';
    }

    public function version(): string
    {
        return '1.0.2';
    }

    public function authors(): array
    {
        return ['Zomb'];
    }

    public function isGame(): bool
    {
        return false;
    }

    public function isCore(): bool
    {
        return false;
    }

    public function menus(): ?array
    {
        return [
            new PackageMenuType(
                icon: 'fas fa-comment',
                title: 'Forum',
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: LangManager::translate('forum.menu.cats'),
                        permission: 'forum.categories',
                        url: 'forum/manage',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('forum.menu.topics'),
                        permission: 'forum.topics',
                        url: 'forum/topics',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('forum.menu.signal'),
                        permission: 'forum.report',
                        url: 'forum/report',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('forum.menu.trash'),
                        permission: 'forum.trash',
                        url: 'forum/trash',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('forum.menu.roles'),
                        permission: 'forum.roles',
                        url: 'forum/roles',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('forum.menu.settings'),
                        permission: 'forum.settings',
                        url: 'forum/settings',
                        subMenus: []
                    ),
                ]
            ),
        ];
    }

    public function requiredPackages(): array
    {
        return ['Core'];
    }

    public function uninstall(): bool
    {
        // Return true, we don't need other operations for uninstall.
        return true;
    }
}
