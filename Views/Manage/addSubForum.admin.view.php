<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Forum\ForumEntity $forum */
/* @var \CMW\Entity\Forum\ForumPermissionRoleEntity[] $ForumRoles */

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span
            class="m-lg-auto">Ajout d'un sous forum dans <?= $forum->getName() ?></span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6 mx-auto">
        <form class="card" method="post">
            <div class="card-body">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <h6>Nom<span style="color: red">*</span> :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="name" required
                           placeholder="Général">
                    <div class="form-control-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                </div>
                <h6>Icon :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="icon"
                           placeholder="fas fa-users">
                    <div class="form-control-icon">
                        <i class="fas fa-icons"></i>
                    </div>
                    <small class="form-text">Retrouvez la liste des icones sur le
                        site de <a href="https://fontawesome.com/search?o=r&m=free"
                                   target="_blank">FontAwesome.com</a></small>
                </div>
                <h6>Déscription :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="description"
                            placeholder="Parlez de tout et de rien">
                    <div class="form-control-icon">
                        <i class="fas fa-paragraph"></i>
                    </div>
                </div>
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input " type="checkbox" id="disallowTopics" name="disallowTopics">
                    <label class="form-check-label" for="disallowTopics"><h6>N'autorise pas la création de nouveau topics</h6></label>
                </div>
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input allowedGroups" type="checkbox" id="allowedGroupsToggle" name="allowedGroupsToggle">
                    <label class="form-check-label" for="allowedGroupsToggle"><h6>Accès restreint</h6></label>
                </div>
                <div class="mt-2" id="listAllowedGroups">
                    <h6>Rôle autorisé :</h6>
                    <div class="form-group">
                        <select class="choices form-select" id="selectBox" name="allowedGroups[]" multiple>
                            <?php foreach ($ForumRoles as $ForumRole): ?>
                                <option
                                    value="<?= $ForumRole->getId() ?>"><?= $ForumRole->getName() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-check"></i>
                    <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                </button>
            </div>
        </form>
    </div>
</section>

<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'App/Package/Forum/Views/Assets/Js/allowedGroups.js' ?>"></script>