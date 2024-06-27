<?php

namespace CMW\Package\Forum;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return "Forum";
    }

    public function version(): string
    {
        return "0.0.1";
    }

    public function authors(): array
    {
        return ["CraftMyWebsite Team"];
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
                lang: "fr",
                icon: "fas fa-comment",
                title: "Forum",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Catégories et forums',
                        permission: 'forum.categories',
                        url: 'forum/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Topics',
                        permission: 'forum.topics',
                        url: 'forum/topics',
                    ),
                    new PackageSubMenuType(
                        title: 'Signalement',
                        permission: 'forum.report',
                        url: 'forum/report',
                    ),
                    new PackageSubMenuType(
                        title: 'Corbeille',
                        permission: 'forum.trash',
                        url: 'forum/trash',
                    ),
                    new PackageSubMenuType(
                        title: 'Rôles et utilisateurs',
                        permission: 'forum.roles',
                        url: 'forum/roles',
                    ),
                    new PackageSubMenuType(
                        title: 'Paramètres',
                        permission: 'forum.settings',
                        url: 'forum/settings',
                    ),
                ]
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-comment",
                title: "Forum",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Categories and forums',
                        permission: 'forum.categories',
                        url: 'forum/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Topics',
                        permission: 'forum.topics',
                        url: 'forum/topics',
                    ),
                    new PackageSubMenuType(
                        title: 'Reports',
                        permission: 'forum.report',
                        url: 'forum/report',
                    ),
                    new PackageSubMenuType(
                        title: 'Trash',
                        permission: 'forum.trash',
                        url: 'forum/trash',
                    ),
                    new PackageSubMenuType(
                        title: 'Roles and users',
                        permission: 'forum.roles',
                        url: 'forum/roles',
                    ),
                    new PackageSubMenuType(
                        title: 'Settings',
                        permission: 'forum.settings',
                        url: 'forum/settings',
                    ),
                ]
            ),
        ];
    }

    public function requiredPackages(): array
    {
        return ["Core"];
    }

    public function uninstall(): bool
    {
        //Return true, we don't need other operations for uninstall.
        return true;
    }
}