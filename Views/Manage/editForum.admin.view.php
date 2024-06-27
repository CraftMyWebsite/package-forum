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

<h3><i class="fa-solid fa-book"></i> <?=LangManager::translate("forum.manage.editForum.text.edit")?> <?= $forum->getName() ?></h3>

<div class="center-flex">
    <div class="flex-content-lg">
        <form class="card space-y-4" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div>
                <label for="name"><?=LangManager::translate("forum.manage.addForum.name")?><span style="color: red">*</span> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-heading"></i>
                    <input type="text" id="name" name="name" value="<?= $forum->getName() ?>" required
                           placeholder="Général">
                </div>
            </div>
            <div class="icon-picker" data-id="icon" data-name="icon" data-label="Icon :" data-placeholder="Sélectionner un icon" data-value="<?= $forum->getIcon() ?>"></div>
            <div>
                <label for="description"><?=LangManager::translate("forum.manage.addForum.desc")?></label>
                <div class="input-group">
                    <i class="fa-solid fa-paragraph"></i>
                    <input type="text" id="description" value="<?= $forum->getDescription() ?>" name="description"
                           placeholder=<?=LangManager::translate("forum.manage.addForum.espDesc")?>>
                </div>
            </div>
            <div>
                <label class="toggle">
                    <p class="toggle-label"><?=LangManager::translate("forum.manage.addForum.noNewTopics")?></p>
                    <input type="checkbox" <?= $forum->disallowTopics() ? 'checked' : '' ?> class="toggle-input" id="disallowTopics" name="disallowTopics">
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <div>
                <label class="toggle">
                    <p class="toggle-label"><?=LangManager::translate("forum.manage.addForum.noAcess")?></p>
                    <input type="checkbox" <?= $forum->isRestricted() ? 'checked' : '' ?> class="toggle-input allowedGroups" id="allowedGroupsToggle" name="allowedGroupsToggle">
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <div id="listAllowedGroups">
                <label><?=LangManager::translate("forum.manage.addForum.role")?></label>
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
            <button type="submit" class="btn-primary btn-center">
                <?= LangManager::translate("core.btn.edit") ?>
            </button>
        </form>
    </div>
</div>


<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'App/Package/Forum/Views/Assets/Js/allowedGroups.js' ?>"></script>