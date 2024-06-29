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
                code: 'forum.categories',
                description: "Catégories et Forums",
            ),
            new PermissionInitType(
                code: 'forum.categories.add',
                description: "Ajouter",
            ),
            new PermissionInitType(
                code: 'forum.categories.edit',
                description: "Éditer",
            ),
            new PermissionInitType(
                code: 'forum.categories.delete',
                description: "Supprimer",
            ),
            /*TOPICS*/
            new PermissionInitType(
                code: 'forum.topics',
                description: "Gérer les topics",
            ),
            /*REPORT*/
            new PermissionInitType(
                code: 'forum.report',
                description: "Gérer les signalements",
            ),
            /*ROLES*/
            new PermissionInitType(
                code: 'forum.roles',
                description: "Gérer les rôles",
            ),
            /*SETTINGS*/
            new PermissionInitType(
                code: 'forum.settings',
                description: "Gérer les paramètres",
            ),
            /*TRASH*/
            new PermissionInitType(
                code: 'forum.trash',
                description: "Gérer la corbeille",
            ),
        ];
    }

}