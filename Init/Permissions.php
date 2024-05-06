<?php

namespace CMW\Permissions\Forum;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Permission\IPermissionInit;
use CMW\Manager\Permission\PermissionInitType;

class Permissions implements IPermissionInit
{
    public function permissions(): array
    {
        return [
            new PermissionInitType(
                code: 'forum.categories.list',
                description: LangManager::translate('forum.permissions.forum.categories.list'),
            ),
            new PermissionInitType(
                code: 'forum.categories.add',
                description: LangManager::translate('forum.permissions.forum.categories.add'),
            ),
            new PermissionInitType(
                code: 'forum.categories.delete',
                description: LangManager::translate('forum.permissions.forum.categories.delete'),
            ),
            new PermissionInitType(
                code: 'forum.add',
                description: LangManager::translate('forum.permissions.forum.add'),
            ),
            new PermissionInitType(
                code: 'forum.delete',
                description: LangManager::translate('forum.permissions.forum.delete'),
            ),
        ];
    }

}