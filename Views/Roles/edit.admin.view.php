<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Forum\ForumPermissionRoleModel;

$title = "Forum - Rôles";
$description = "desc";

$test = ForumPermissionRoleModel::getInstance();
/* @var CMW\Entity\Forum\ForumPermissionRoleEntity $role */

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gavel"></i> <span class="m-lg-auto">Modification du rôle <?= $role->getName() ?></span></h3>
    <div class="buttons">
        <button form="add_role" type="submit"
                class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
    </div>
</div>

<form id="add_role" method="post" action="" class="row">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <div class="col-12 col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-12 col-lg-6">
                        <input hidden value="<?= $role->getWeight() ?>" type="number" name="weight" class="form-control"
                               placeholder="1"
                               required>
                        <h6>Nom :</h6>
                        <div class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control" name="name" value="<?= $role->getName() ?>" placeholder="Community Manager"
                                   required>
                            <div class="form-control-icon">
                                <i class="fa-solid fa-id-card-clip"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h6>Description :</h6>
                        <div class="form-group position-relative has-icon-left">
                            <input value="<?= $role->getDescription() ?>" type="text" class="form-control" name="description"
                                   placeholder="Gère la communauté" required>
                            <div class="form-control-icon">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h4 class="text-center">Permissions</h4>
                <h5 class="mt-2">Administrateur</h5>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="operator" name="operator" <?= $test->roleHasPerm($role->getId(),1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="operator"><h6>Toutes les permissions</h6></label>
                </div>
                <hr>
                <h5 class="mt-2">Modérateur</h5>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="admin_change_topic_name" name="admin_change_topic_name" <?= $test->roleHasPerm($role->getId(),18) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="admin_change_topic_name"><h6>Changer le nom des topics</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="admin_change_topic_tag" name="admin_change_topic_tag" <?= $test->roleHasPerm($role->getId(),19) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="admin_change_topic_tag"><h6>Changer les tags des topics</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="admin_change_topic_prefix" name="admin_change_topic_prefix" <?= $test->roleHasPerm($role->getId(),20) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="admin_change_topic_prefix"><h6>Changer les préfixes des topics</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="admin_move_topic" name="admin_move_topic" <?= $test->roleHasPerm($role->getId(),24) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="admin_move_topic"><h6>Déplacer des topics</h6></label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="admin_set_important" name="admin_set_important" <?= $test->roleHasPerm($role->getId(),21) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="admin_set_important"><h6>Définir sur important / non important</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="admin_set_pin" name="admin_set_pin" <?= $test->roleHasPerm($role->getId(),22) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="admin_set_pin"><h6>Définir sur épingler / désépingler</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="admin_set_closed" name="admin_set_closed" <?= $test->roleHasPerm($role->getId(),23) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="admin_set_closed"><h6>Définir sur clos / ouvert</h6></label>
                        </div>
                    </div>
                </div>
                <hr>
                <h5 class="mt-2">Utilisateur</h5>
                <h6 class="text-center">Forum</h6>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_view_forum" name="user_view_forum" <?= $test->roleHasPerm($role->getId(),2) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_view_forum"><h6>Consulter le forum</h6></label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_view_topic" name="user_view_topic" <?= $test->roleHasPerm($role->getId(),3) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_view_topic"><h6>Consulter des topic</h6></label>
                        </div>
                    </div>
                </div>
                <h6 class="text-center">Topic</h6>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_create_topic" name="user_create_topic" <?= $test->roleHasPerm($role->getId(),4) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_create_topic"><h6>Créer des topics</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_edit_topic" name="user_edit_topic" <?= $test->roleHasPerm($role->getId(),7) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_edit_topic"><h6>Éditer ses topics</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_create_topic_tag" name="user_create_topic_tag" <?= $test->roleHasPerm($role->getId(),5) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_create_topic_tag"><h6>Créer des Tags sur ces topics</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_edit_tag" name="user_edit_tag" <?= $test->roleHasPerm($role->getId(),8) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_edit_tag"><h6>Éditer ses Tags</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_create_pool" name="user_create_pool" <?= $test->roleHasPerm($role->getId(),6) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_create_pool"><h6>Créer des sondages</h6></label>
                        </div>

                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_edit_pool" name="user_edit_pool" <?= $test->roleHasPerm($role->getId(),9) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_edit_pool"><h6>Éditer ses sondages</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_react_topic" name="user_react_topic" <?= $test->roleHasPerm($role->getId(),11) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_react_topic"><h6>Réagir à un topic</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_change_react_topic" name="user_change_react_topic" <?= $test->roleHasPerm($role->getId(),12) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_change_react_topic"><h6>Changer sa réaction sur un topic</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_remove_react_topic" name="user_remove_react_topic" <?= $test->roleHasPerm($role->getId(),13) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_remove_react_topic"><h6>Supprimé sa réaction sur un topic</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_remove_topic" name="user_remove_topic" <?= $test->roleHasPerm($role->getId(),10) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_remove_topic"><h6>Supprimé ses topics</h6></label>
                        </div>
                    </div>
                </div>
                <h6 class="text-center">Réponses</h6>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_response_topic" name="user_response_topic" <?= $test->roleHasPerm($role->getId(),14) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_response_topic"><h6>Répondre à un topic</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_response_react" name="user_response_react" <?= $test->roleHasPerm($role->getId(),15) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_response_react"><h6>Réagir à des réponses</h6></label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_response_change_react" name="user_response_change_react" <?= $test->roleHasPerm($role->getId(),16) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_response_change_react"><h6>Changer se réaction sur des réponses</h6></label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="user_response_remove_react" name="user_response_remove_react" <?= $test->roleHasPerm($role->getId(),17) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="user_response_remove_react"><h6>Supprimé sa réaction sur des méssages</h6></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'App/Package/Forum/Views/Assets/Js/permCheck.js' ?>"></script>