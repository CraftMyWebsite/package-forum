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
/* @var \CMW\Model\Forum\ForumUserBlockedModel $userBlocked */

?>

<h3><i class="fa-solid fa-gavel"></i> Rôles</h3>

<div class="grid-2">
    <div>
        <div class="card">
            <div class="lg:flex justify-between">
                <h6>Rôles</h6>
                <a type="button" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/forum/roles/add"
                   class="btn-primary"><?= LangManager::translate("core.btn.add") ?></a>
            </div>
            <div class="table-container">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Rôle par défaut</th>
                        <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($roles as $role) : ?>
                        <tr>
                            <td><?= $role->getName() ?></td>
                            <td><?= $role->getDescription() ?></td>
                            <td><?php if ($role->isDefault()): ?>
                                    <i class="text-success fa-regular fa-circle-dot fa-beat-fade"></i>
                                <?php else: ?>
                                    <button type="button" data-modal-toggle="modal-setdefault-<?= $role->getId() ?>">
                                        <i class="fa-regular fa-circle fa-2xs text-primary"><span hidden>a</span></i>
                                    </button>
                                    <div id="modal-setdefault-<?= $role->getId() ?>" class="modal-container">
                                        <div class="modal">
                                            <div class="modal-header">
                                                <h6>Faut il appliqué ce paramètres à tout le monde ?</h6>
                                                <button type="button" data-modal-hide="modal-setdefault-<?= $role->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                            <div class="modal-body">
                                                Voulez-vous également appliquer ce rôle par default à tout les
                                                utilisateurs qui ont déjà le rôle par defaut ?<br>
                                                Attention si vous choisissez "Non" vous devrez le faire
                                                manuellement.
                                            </div>
                                            <div class="modal-footer">
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
                                <?php endif; ?></td>
                            <td class="text-center space-x-2">
                                <a class=""
                                   href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/forum/roles/edit/<?= $role->getId() ?>">
                                    <i class="text-info fa-solid fa-gears"></i>
                                </a>
                                <button type="button" data-modal-toggle="modal-delete-<?= $role->getId() ?>">
                                    <i class="text-danger fas fa-trash-alt"></i>
                                </button>
                                <div id="modal-delete-<?= $role->getId() ?>" class="modal-container">
                                    <div class="modal">
                                        <div class="modal-header-danger">
                                            <h6>Supprimé : <?= $role->getName() ?> ?</h6>
                                            <button type="button" data-modal-hide="modal-delete-<?= $role->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                        </div>
                                        <div class="modal-body">
                                            voulez vous vraiment supprimé ce rôle ?
                                        </div>
                                        <div class="modal-footer">
                                            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/forum/roles/delete/<?= $role->getId() ?>"
                                               class="btn-danger">
                                                <span class="">Supprimé</span>
                                            </a>
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
        <div class="card mt-4">
            <h6>Paramètres</h6>
            <form action="roles/settings" method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <div>
                    <label class="toggle">
                        <p class="toggle-label">Accès en
                            lecture pour les vitisteurs
                            <i data-bs-toggle="tooltip"
                               title="Si cette option est active la permission de consulter le forum pour les rôle n'est plus active, cette option est prioritaire"
                               class="fa-sharp fa-solid fa-circle-question"></i></p>
                        <input type="checkbox" class="toggle-input" id="visitorCanViewForum"
                               name="visitorCanViewForum" <?= $visitorCanViewForum ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                    </label>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <?= LangManager::translate("core.btn.save") ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <h6>Rôle des utilisateurs</h6>
        <div class="table-container">
            <table class="table" id="table2">
                <thead>
                <tr>
                    <th>Pseudo</th>
                    <th>Rôle du site</th>
                    <th>Rôle forum</th>
                    <th>Bloqué</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($userList as $user) : ?>
                    <tr>
                        <td><?= $user->getPseudo() ?></td>
                        <td><small><?= $user->getHighestRole()->getName() ?></small></td>
                        <td><b><?= $userRole->getHighestRoleByUser($user->getId())->getName() ?></b></td>
                        <td>
                            <?php if ($userBlocked->getUserBlockedByUserId($user->getId())->isBlocked()): ?>
                                <span class="text-danger">Oui</span>
                                <i data-bs-toggle="tooltip"
                                   title="<?= $userBlocked->getUserBlockedByUserId($user->getId())->getReason() ?>"
                                   class="fa-sharp fa-solid fa-circle-question"></i>
                            <?php else: ?>
                                <span class="text-success">Non</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center space-x-2">
                            <?php if ($userBlocked->getUserBlockedByUserId($user->getId())->isBlocked()): ?>
                                <button class="text-center me-2" type="button" data-modal-toggle="modal-unblock-user-<?= $user->getId()?>">
                                    <i class="text-success fa-solid fa-user-check"></i>
                                </button>
                            <?php else: ?>
                                <button class="text-center me-2" type="button" data-modal-toggle="modal-block-user-<?= $user->getId()?>">
                                    <i class="text-danger fa-solid fa-gavel"></i>
                                </button>
                            <?php endif; ?>
                            <button class="text-center" type="button" data-modal-toggle="modal-edit-user-role-<?= $user->getId()?>">
                                <i class="text-info fa-solid fa-edit"></i>
                            </button>
                        </td>
                    </tr>

                    <!------MODAL EDIT ROLE FOR USER ------>
                    <div id="modal-edit-user-role-<?= $user->getId()?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Modifier le rôle de <?= $user->getPseudo() ?></h6>
                                <button type="button" data-modal-hide="modal-edit-user-role-<?= $user->getId()?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <form action="roles/user_role/<?= $user->getId() ?>" method="post">
                                <?php (new SecurityManager())->insertHiddenToken() ?>
                            <div class="modal-body">
                                <label>Nouveau rôle :</label>
                                <select class="form-select" name="role_id" required>
                                    <?php foreach ($roles as $role) : ?>
                                        <option value="<?= $role->getId() ?>"
                                            <?= ($userRole->getHighestRoleByUser($user->getId())->getName() === $role->getName() ? "selected" : "") ?>>
                                            <?= $role->getName() ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn-primary">
                                    <?= LangManager::translate("core.btn.edit") ?>
                                </button>
                            </div>
                            </form>
                        </div>
                    </div>
                    <!------MODAL BLOCK USER ------>
                    <div id="modal-block-user-<?= $user->getId()?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6>Bloquer <?= $user->getPseudo() ?></h6>
                                <button type="button" data-modal-hide="modal-block-user-<?= $user->getId()?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <form action="roles/block/<?= $user->getId() ?>" method="post">
                                <?php (new SecurityManager())->insertHiddenToken() ?>
                            <div class="modal-body">
                                <label>Raison :</label>
                                <input type="text" class="input" name="reason" value="<?= $userBlocked->getUserBlockedByUserId($user->getId())->getReason() ?>" placeholder=""
                                       required>
                                <div class="mt-4">
                                <span>Votre utilisateur ne pourra plus :</span>
                                <ul style="list-style: circle" class="pl-6">
                                    <li>Créer des topics</li>
                                    <li>Éditer ses topics</li>
                                    <li>Supprimer ses topics</li>
                                    <li>Réagir à des topics</li>
                                    <li>Répondre à des topics</li>
                                    <li>Éditer ses réponses</li>
                                    <li>Supprimer ses réponses</li>
                                    <li>Réagir à des réponses</li>
                                </ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">
                                    Bloquer
                                </button>
                            </div>
                            </form>
                        </div>
                    </div>
                    <!------MODAL UNBLOCK USER ------>
                    <div id="modal-unblock-user-<?= $user->getId()?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-success">
                                <h6>Débloquer <?= $user->getPseudo() ?></h6>
                                <button type="button" data-modal-hide="modal-unblock-user-<?= $user->getId()?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <form action="roles/unblock/<?= $user->getId() ?>" method="post">
                                <?php (new SecurityManager())->insertHiddenToken() ?>
                            <div class="modal-body">
                                <label>Raison :</label>
                                <input type="text" class="input" name="reason" value="<?= $userBlocked->getUserBlockedByUserId($user->getId())->getReason() ?>" placeholder=""
                                       required>
                                <div class="mt-4">
                                    <span>Votre utilisateur pourra à nouveau :</span>
                                    <ul style="list-style: circle" class="pl-6">
                                        <li>Créer des topics</li>
                                        <li>Éditer ses topics</li>
                                        <li>Supprimer ses topics</li>
                                        <li>Réagir à des topics</li>
                                        <li>Répondre à des topics</li>
                                        <li>Éditer ses réponses</li>
                                        <li>Supprimer ses réponses</li>
                                        <li>Réagir à des réponses</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn-success">
                                    Débloquer
                                </button>
                            </div>
                            </form>
                        </div>
                    </div>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
