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
        return "1.0.0";
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
                        permission: 'forum.categories.list',
                        url: 'forum/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Topics',
                        permission: 'forum.categories.list', //TODO PERMS
                        url: 'forum/topics',
                    ),
                    new PackageSubMenuType(
                        title: 'Signalement',
                        permission: 'forum.categories.list', //TODO PERMS
                        url: 'forum/report',
                    ),
                    new PackageSubMenuType(
                        title: 'Rôles et utilisateurs',
                        permission: 'forum.categories.list', //TODO PERMS
                        url: 'forum/roles',
                    ),
                    new PackageSubMenuType(
                        title: 'Paramètres',
                        permission: 'forum.categories.list', //TODO PERMS
                        url: 'forum/settings',
                    ),
                    new PackageSubMenuType(
                        title: 'Corbeille',
                        permission: 'forum.delete',
                        url: 'forum/trash',
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
                        permission: 'forum.categories.list',
                        url: 'forum/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Topics',
                        permission: 'forum.categories.list', //TODO PERMS
                        url: 'forum/topics',
                    ),
                    new PackageSubMenuType(
                        title: 'Reports',
                        permission: 'forum.categories.list', //TODO PERMS
                        url: 'forum/report',
                    ),
                    new PackageSubMenuType(
                        title: 'Roles and users',
                        permission: 'forum.categories.list', //TODO PERMS
                        url: 'forum/roles',
                    ),
                    new PackageSubMenuType(
                        title: 'Settings',
                        permission: 'forum.categories.list', //TODO PERMS
                        url: 'forum/settings',
                    ),
                    new PackageSubMenuType(
                        title: 'Trash',
                        permission: 'forum.delete',
                        url: 'forum/trash',
                    ),
                ]
            ),
        ];
    }
}