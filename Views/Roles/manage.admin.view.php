<?php


use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "Forum - Rôles";
$description = "desc";

/* @var CMW\Model\Forum\ForumSettingsModel $visitorCanViewForum */
/* @var CMW\Entity\Forum\ForumPermissionRoleEntity[] $roles */
/* @var \CMW\Entity\Users\UserEntity[] $userList */
/* @var \CMW\Model\Forum\ForumPermissionRoleModel $userRole */

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gavel"></i> <span class="m-lg-auto">Rôles</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Rôles</h4>
            </div>
            <div class="position-absolute end-0">
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/forum/roles/add"
                   class="text-bg-primary rounded-2 py-1 px-2"><?= LangManager::translate("core.btn.add") ?></a>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Rôle par défaut</th>
                        <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($roles as $role) : ?>
                        <tr>
                            <td><?= $role->getName() ?></td>
                            <td><?= $role->getDescription() ?></td>
                            <td><?php if ($role->isDefault()): ?>
                                    <i class="text-success fa-regular fa-circle-dot fa-beat-fade"></i>
                                <?php else: ?>
                                    <a type="button" data-bs-toggle="modal"
                                       data-bs-target="#setdefault-<?= $role->getId() ?>">
                                        <i class="fa-regular fa-circle fa-2xs text-primary"><span hidden>a</span></i>
                                    </a>
                                    <div class="modal fade text-left" id="setdefault-<?= $role->getId() ?>"
                                         tabindex="-1"
                                         role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                             role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h5 class="modal-title white" id="myModalLabel160">
                                                        Faut il appliqué ce paramètres à tout le monde ?</h5>
                                                </div>
                                                <div class="modal-body text-left">
                                                    Voulez-vous également appliquer ce rôle par default à tout les
                                                    utilisateurs qui ont déjà le rôle par defaut ?<br>
                                                    Attention si vous choisissez "Non" vous devrez le faire
                                                    manuellement.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light-secondary"
                                                            data-bs-dismiss="modal">
                                                        <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                    </button>
                                                    <a href="roles/set_default/<?= $role->getId() ?>/no"
                                                       class="btn btn-warning">
                                                        <span class="">Non</span>
                                                    </a>
                                                    <a href="roles/set_default/<?= $role->getId() ?>/yes"
                                                       class="btn btn-success">
                                                        <span class="">Oui</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?></td>
                            <td>
                                <a class="me-3 "
                                   href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/forum/roles/edit/<?= $role->getId() ?>">
                                    <i class="text-primary fa-solid fa-gears"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $role->getId() ?>">
                                    <i class="text-danger fas fa-trash-alt"></i>
                                </a>
                                <div class="modal fade text-left" id="delete-<?= $role->getId() ?>" tabindex="-1"
                                     role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                         role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title white" id="myModalLabel160">
                                                    Supprimé : <?= $role->getName() ?> ?</h5>
                                            </div>
                                            <div class="modal-body text-left">
                                                voulez vous vraiment supprimé ce rôle ?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light-secondary"
                                                        data-bs-dismiss="modal">
                                                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                </button>
                                                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/forum/roles/delete/<?= $role->getId() ?>"
                                                   class="btn btn-danger">
                                                    <span class="">Supprimé</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Rôle des utilisateurs</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table2">
                    <thead>
                    <tr>
                        <th class="text-center">Pseudo</th>
                        <th class="text-center">Rôle du site</th>
                        <th class="text-center">Rôle forum</th>
                        <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($userList as $user) : ?>
                        <tr>
                            <td><?= $user->getPseudo() ?></td>
                            <td><small><?= $user->getHighestRole()->getName() ?></small></td>
                            <td><b><?= $userRole->getHighestRoleByUser($user->getId())->getName() ?></b></td>
                            <td>
                                <a class="text-center" type="button" data-bs-toggle="modal" data-bs-target="#edit-user-role">
                                    <i class="text-primary fa-solid fa-edit"></i>
                                </a>
                            </td>
                        </tr>

                        <!--
                        ----MODAL EDIT ROLE FOR USER ----
                        -->
                        <div class="modal fade text-left" id="edit-user-role" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Modifier le rôle de <?= $user->getPseudo() ?></h5>
                                    </div>
                                    <form action="roles/user_role/<?= $user->getId() ?>" method="post">
                                        <?php (new SecurityManager())->insertHiddenToken() ?>
                                    <div class="modal-body">
                                        <h6>Nouveau rôle :</h6>
                                        <select class="form-select" name="role_id" required>
                                            <?php foreach ($roles as $role) : ?>
                                                <option value="<?= $role->getId() ?>"
                                                    <?= ($userRole->getHighestRoleByUser($user->getId())->getName() === $role->getName() ? "selected" : "") ?>>
                                                    <?= $role->getName() ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.edit") ?></span>
                                        </button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


<div class="col-12 col-lg-6">
    <div class="card ">
        <div class="card-header">
            <h6>Paramètres</h6>
        </div>
        <div class="card-body">
            <form action="roles/settings" method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <div class="row">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" value="1" type="checkbox" id="visitorCanViewForum"
                               name="visitorCanViewForum" <?= $visitorCanViewForum ? 'checked' : '' ?>>
                        <label class="form-check-label" for="visitorCanViewForum">Accès en
                            lecture pour les vitisteurs
                            <i data-bs-toggle="tooltip"
                               title="Si cette option est active la permission de consulter le forum pour les rôle n'est plus active, cette option est prioritaire"
                               class="fa-sharp fa-solid fa-circle-question"></i></label>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <?= LangManager::translate("core.btn.save") ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>