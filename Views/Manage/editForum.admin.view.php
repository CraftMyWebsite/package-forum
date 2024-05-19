<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Forum\ForumEntity $forum */
/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var \CMW\Entity\Forum\ForumPermissionRoleEntity[] $ForumRoles */

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span
            class="m-lg-auto"><?=LangManager::translate("forum.manage.editForum.text.edit")?> <?= $forum->getName() ?></span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6 mx-auto">
        <form class="card" method="post">
            <div class="card-body">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <h6>Icon :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" value="<?= $forum->getIcon() ?>" name="icon" required
                           placeholder="fas fa-users">
                    <div class="form-control-icon">
                        <i class="fas fa-icons"></i>
                    </div>
                    <small class="form-text"><?=LangManager::translate("forum.manage.editForum.text.icon")?> <a href="https://fontawesome.com/search?o=r&m=free"
                                   target="_blank">FontAwesome.com</a></small>
                </div>
                <h6><?=LangManager::translate("forum.manage.editForum.text.name")?></h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" value="<?= $forum->getName() ?>" class="form-control" name="name" required
                           placeholder="Général">
                    <div class="form-control-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                </div>
                <h6><?=LangManager::translate("forum.manage.editForum.text.desc")?></h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" value="<?= $forum->getDescription() ?>" class="form-control" name="description"
                           required placeholder="<?=LangManager::translate("forum.manage.editForum.text.espDesc")?>">
                    <div class="form-control-icon">
                        <i class="fas fa-paragraph"></i>
                    </div>
                </div>
                <div class="form-check form-switch mt-4">
                    <input <?= $forum->disallowTopics() ? 'checked' : '' ?> class="form-check-input " type="checkbox" id="disallowTopics" name="disallowTopics">
                    <label class="form-check-label" for="disallowTopics"><h6><?=LangManager::translate("forum.manage.editForum.text.noNewTopics")?></h6></label>
                </div>
                <div class="form-check form-switch mt-4">
                    <input <?= $forum->isRestricted() ? 'checked' : '' ?> class="form-check-input allowedGroups" type="checkbox" id="allowedGroupsToggle" name="allowedGroupsToggle">
                    <label class="form-check-label" for="allowedGroupsToggle"><h6><?=LangManager::translate("forum.manage.editForum.text.noAcess")?></h6></label>
                </div>
                <div class="mt-2" id="listAllowedGroups">
                    <h6><?=LangManager::translate("forum.manage.editForum.text.role")?></h6>
                    <div class="form-group">
                        <select class="choices form-select" id="selectBox" name="allowedGroups[]" multiple>
                            <?php foreach ($ForumRoles as $ForumRole): ?>
                                <option
                                    <?php foreach ($forumModel::getInstance()->getAllowedRoles($forum->getId()) as $allowedRoles): ?>
                                        <?= $allowedRoles->getName() === $ForumRole->getName() ? 'selected' : '' ?>
                                    <?php endforeach ?>
                                    value="<?= $ForumRole->getId() ?>"><?= $ForumRole->getName() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-check"></i>
                    <span class=""><?= LangManager::translate("core.btn.edit") ?></span>
                </button>
            </div>
        </form>
    </div>
</section>

<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'App/Package/Forum/Views/Assets/Js/allowedGroups.js' ?>"></script>