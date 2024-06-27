<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Forum\ForumPermissionRoleModel;

$title = "Forum - Rôles";
$description = "desc";

$roleModel = ForumPermissionRoleModel::getInstance();
/* @var CMW\Entity\Forum\ForumPermissionRoleEntity $role */

?>


<div class="page-title">
    <h3><i class="fa-solid fa-gavel"></i> Modification du rôle <?= $role->getName() ?> </h3>
    <button form="add_role" type="submit" class="btn-primary"><?= LangManager::translate("core.btn.edit") ?></button>
</div>

<form id="add_role" method="post" action="" class="card">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <input type="number" value="<?= $role->getWeight() ?>" hidden name="weight" class="form-control"
           placeholder="1"
           required>
    <div class="grid-2">
        <div>
            <label for="name">Nom :</label>
            <div class="input-group">
                <i class="fa-solid fa-id-card-clip"></i>
                <input type="text" value="<?= $role->getName() ?>" id="name" name="name" placeholder="Community Manager"
                       required>
            </div>
        </div>
        <div>
            <label for="description">Description :</label>
            <div class="input-group">
                <i class="fa-solid fa-circle-info"></i>
                <input type="text" value="<?= $role->getDescription() ?>" id="description" name="description"
                       placeholder="Gère la communauté" required>
            </div>
        </div>
    </div>
    <hr>
    <h4 class="text-center">Permissions</h4>

    <div class="center-flex">
        <div class="flex-content-lg">
            <h5 class="mt-2">Administrateur</h5>
            <label class="toggle">
                <input type="checkbox" class="toggle-input" id="operator" name="operator" <?= $roleModel->roleHasPerm($role->getId(),1) ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
                <p class="toggle-label">Toutes les permissions</p>
            </label>
            <hr>
            <label class="toggle mb-4">
                <input type="checkbox" class="toggle-input" id="moderator_all_check" name="moderator_all_check">
                <div class="toggle-slider"></div>
                <h6 class="toggle-label">Modérateur</h6>
            </label>

            <div class="grid-2">
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="admin_change_topic_name" name="admin_change_topic_name" <?= $roleModel->roleHasPerm($role->getId(),18) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Changer le nom des topics</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="admin_change_topic_tag" name="admin_change_topic_tag" <?= $roleModel->roleHasPerm($role->getId(),19) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Changer les tags des topics</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="admin_change_topic_prefix"
                           name="admin_change_topic_prefix" <?= $roleModel->roleHasPerm($role->getId(),20) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Changer les préfixes des topics</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="admin_move_topic" name="admin_move_topic" <?= $roleModel->roleHasPerm($role->getId(),24) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Déplacer des topics</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="admin_set_important" name="admin_set_important" <?= $roleModel->roleHasPerm($role->getId(),21) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Définir sur important / non important</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="admin_set_pin" name="admin_set_pin" <?= $roleModel->roleHasPerm($role->getId(),22) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Définir sur épingler / désépingler</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="admin_set_closed" name="admin_set_closed" <?= $roleModel->roleHasPerm($role->getId(),23) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Définir sur clos / ouvert</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="admin_bypass_forum_disallow_topics"
                           name="admin_bypass_forum_disallow_topics" <?= $roleModel->roleHasPerm($role->getId(),25) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Poster des topics dans les forums clos</p>
                </label>
            </div>
            <hr>
            <label class="toggle">
                <input type="checkbox" class="toggle-input" id="user_all_check" name="user_all_check">
                <div class="toggle-slider"></div>
                <h6 class="toggle-label">Utilisateur</h6>
            </label>
            <h6 class="text-center mt-4">Forum</h6>
            <div class="grid-2">
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_view_forum" name="user_view_forum" <?= $roleModel->roleHasPerm($role->getId(),2) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Consulter le forum</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_view_topic" name="user_view_topic" <?= $roleModel->roleHasPerm($role->getId(),3) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Consulter des topic</p>
                </label>
            </div>
            <h6 class="text-center mt-4">Topic</h6>
            <div class="grid-2">
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_create_topic" name="user_create_topic" <?= $roleModel->roleHasPerm($role->getId(),4) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Créer des topics</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_edit_topic" name="user_edit_topic" <?= $roleModel->roleHasPerm($role->getId(),7) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Éditer ses topics</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_create_topic_tag" name="user_create_topic_tag" <?= $roleModel->roleHasPerm($role->getId(),5) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Créer des Tags sur ces topics</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_edit_tag" name="user_edit_tag" <?= $roleModel->roleHasPerm($role->getId(),8) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Éditer ses Tags</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_create_pool" name="user_create_pool" <?= $roleModel->roleHasPerm($role->getId(),6) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Créer des sondages</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_edit_pool" name="user_edit_pool" <?= $roleModel->roleHasPerm($role->getId(),9) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Éditer ses sondages</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_react_topic" name="user_react_topic" <?= $roleModel->roleHasPerm($role->getId(),11) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Réagir à un topic</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_change_react_topic" name="user_change_react_topic" <?= $roleModel->roleHasPerm($role->getId(),12) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Changer sa réaction sur un topic</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_remove_react_topic" name="user_remove_react_topic" <?= $roleModel->roleHasPerm($role->getId(),13) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Supprimé sa réaction sur un topic</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_remove_topic" name="user_remove_topic" <?= $roleModel->roleHasPerm($role->getId(),10) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Supprimé ses topics</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_add_file" name="user_add_file" <?= $roleModel->roleHasPerm($role->getId(),28) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Ajouter des fichiers</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_download_file" name="user_download_file" <?= $roleModel->roleHasPerm($role->getId(),29) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Télécharger des fichiers</p>
                </label>
            </div>
            <h6 class="text-center mt-4">Réponses</h6>
            <div class="grid-2">
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_response_topic" name="user_response_topic" <?= $roleModel->roleHasPerm($role->getId(),14) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Répondre à un topic</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_response_react" name="user_response_react" <?= $roleModel->roleHasPerm($role->getId(),15) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Réagir à des réponses</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_edit_response" name="user_edit_response" <?= $roleModel->roleHasPerm($role->getId(),27) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Éditer ses réponses</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_response_change_react"
                           name="user_response_change_react" <?= $roleModel->roleHasPerm($role->getId(),16) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Changer se réaction sur des réponses</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_response_remove_react"
                           name="user_response_remove_react" <?= $roleModel->roleHasPerm($role->getId(),17) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Supprimé sa réaction sur des méssages</p>
                </label>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" id="user_remove_response" name="user_remove_response" <?= $roleModel->roleHasPerm($role->getId(),26) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label">Supprimé ses réponses</p>
                </label>
            </div>
        </div>
    </div>
</form>


<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'App/Package/Forum/Views/Assets/Js/permCheck.js' ?>"></script>