<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Forum\ForumPrefixModel;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var CMW\Model\Forum\ForumCategoryModel $categoryModel */
/* @var \CMW\Entity\Forum\ForumPermissionRoleEntity[] $ForumRoles */

$title = LangManager::translate("forum.manage.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>

<div class="page-title">
    <h3><i class="fa-solid fa-book"></i> <?= LangManager::translate("forum.manage.list.title") ?></h3>
    <button data-modal-toggle="modal-add-cat" class="btn-primary" type="button"><i
            class="fa-solid fa-circle-plus"></i> <?= LangManager::translate("forum.manage.list.buton.addCat") ?>
    </button>
</div>

<?php if ($categoryModel->getCategories()): ?>
    <div>
        <?php foreach ($categoryModel->getCategories() as $category): ?>
            <div class="card mb-6">
                <div class="flex justify-between hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div>
                        <small><i class="text-secondary fa-solid fa-circle-dot"></i></small>
                        <?= $category->getFontAwesomeIcon() ?> <?= $category->getName() ?>
                        <?php if ($category->isRestricted()): ?><small style="color: #af1a1a">Restreint
                            <i data-bs-toggle="tooltip"
                               title="<?php foreach ($categoryModel->getAllowedRoles($category->getId()) as $allowedRoles): ?> - <?= $allowedRoles->getName() ?> <?php endforeach ?>"
                               class="fa-sharp fa-solid fa-circle-info"></i></small>
                        <?php endif; ?>-
                        <i><small><?= mb_strimwidth($category->getDescription(), 0, 45, '...') ?></small></i>
                    </div>
                    <div class="space-x-2">
                        <a target="_blank"
                           href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . "/forum/c/" . $category->getSlug() ?>"><i
                                class="fa-solid fa-up-right-from-square"></i></a>
                        <a href="manage/addForum/<?= $category->getId() ?>">
                            <i class="text-success fa-solid fa-circle-plus"></i>
                        </a>
                        <button type="button" data-modal-toggle="modal-edit-categories-<?= $category->getId() ?>"><i
                                class="text-info fas fa-edit"></i></button>
                        <button type="button" data-modal-toggle="modal-delete-<?= $category->getId() ?>"><i
                                class="text-danger fas fa-trash-alt"></i></button>
                    </div>
                </div>
                <?php foreach ($forumModel->getForumByCat($category->getId()) as $forumObj): ?>
                    <div class="flex justify-between pl-6 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <div>
                            <small><i class="text-secondary fa-solid fa-turn-up fa-rotate-90"></i></small>
                            <?= $forumObj->getFontAwesomeIcon() ?>
                            <?= $forumObj->getName() ?>
                            <?php if ($forumObj->isRestricted()): ?><small style="color: #af1a1a">Restreint
                                <i data-bs-toggle="tooltip"
                                   title="<?php foreach ($forumModel->getAllowedRoles($forumObj->getId()) as $allowedRoles): ?> - <?= $allowedRoles->getName() ?> <?php endforeach ?>"
                                   class="fa-sharp fa-solid fa-circle-info"></i></small>
                            <?php endif; ?>
                            <?php if ($forumObj->disallowTopics()): ?> - <i data-bs-toggle="tooltip"
                                                                            title=<?= LangManager::translate("forum.manage.list.text.noNewTopics") ?> class="fa-sharp
                                                                            fa-solid
                                                                            fa-lock"></i></small> <?php endif; ?>
                            -
                            <i><small><?= mb_strimwidth($forumObj->getDescription(), 0, 45, '...') ?></small></i>
                        </div>
                        <div class="space-x-2">
                            <a target="_blank"
                               href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . "/forum/c/" . $category->getSlug() . "/f/" . $forumObj->getLink() ?>"><i
                                    class="fa-solid fa-up-right-from-square"></i></a>
                            <a href="manage/addSubForum/<?= $forumObj->getId() ?>">
                                <i class="text-success fas fa-circle-plus"></i>
                            </a>
                            <a href="manage/editForum/<?= $forumObj->getId() ?>">
                                <i class="text-info fas fa-edit"></i>
                            </a>
                            <button type="button" data-modal-toggle="modal-deletee-<?= $forumObj->getId() ?>"><i
                                    class="text-danger fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <!----LISTAGE SOUS-FORUM ---->
                    <?php foreach ($forumModel->getSubsForums($forumObj->getId()) as $subForum): ?>
                        <div class="flex justify-between hover:bg-gray-100 dark:hover:bg-gray-800"
                             style="padding-left: <?= 1 + $subForum["depth"] * 2 ?>rem">
                            <div>
                                <i class="text-secondary fa-solid fa-turn-up fa-rotate-90"></i>
                                <?= $subForum["subforum"]->getFontAwesomeIcon() ?>
                                <?= $subForum["subforum"]->getName() ?>
                                <?php if ($subForum["subforum"]->isRestricted()): ?><small style="color: #af1a1a">
                                    Restreint
                                    <i data-bs-toggle="tooltip"
                                       title="<?php foreach ($forumModel->getAllowedRoles($subForum["subforum"]->getId()) as $allowedRoles): ?> - <?= $allowedRoles->getName() ?> <?php endforeach ?>"
                                       class="fa-sharp fa-solid fa-circle-info"></i></small>
                                <?php endif; ?>
                                <?php if ($subForum["subforum"]->disallowTopics()): ?> - <i data-bs-toggle="tooltip"
                                                                                            title=<?= LangManager::translate("forum.manage.list.text.noNewTopics") ?>> </i></small> <?php endif; ?>
                                    -
                                    <i><small><?= mb_strimwidth($subForum["subforum"]->getDescription(), 0, 45, '...') ?></small></i>
                            </div>
                            <div class="space-x-2">
                                <a target="_blank"
                                   href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . "/forum/c/" . $category->getSlug() . "/f/" . $subForum["subforum"]->getLink() ?>"><i
                                        class="fa-solid fa-up-right-from-square"></i></a>
                                <a href="manage/addSubForum/<?= $subForum["subforum"]->getId() ?>">
                                    <i class="text-success fas fa-circle-plus"></i>
                                </a>
                                <a href="manage/editForum/<?= $subForum["subforum"]->getId() ?>">
                                    <i class="text-info fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        data-modal-toggle="modal-delete-sub-<?= $subForum["subforum"]->getId() ?>"><i
                                        class="text-danger fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                        <!----MODAL SUPRESSION SOUS FORUM---->
                        <div id="modal-delete-sub-<?= $subForum["subforum"]->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-danger">
                                    <h6><?= LangManager::translate("forum.manage.list.modal.delete") ?> <?= $subForum["subforum"]->getName() ?></h6>
                                    <button type="button"
                                            data-modal-hide="modal-delete-sub-<?= $subForum["subforum"]->getId() ?>"><i
                                            class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    <?= LangManager::translate("forum.manage.list.modal.definitiveDelete") ?>
                                </div>
                                <div class="modal-footer">
                                    <a href="manage/deleteForum/<?= $subForum["subforum"]->getId() ?>"
                                       class="btn-danger">
                                        <?= LangManager::translate("core.btn.delete") ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!----MODAL SUPRESSION FORUM---->
                    <div id="modal-deletee-<?= $forumObj->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6><?= LangManager::translate("forum.manage.list.modal.delete") ?> <?= $forumObj->getName() ?></h6>
                                <button type="button" data-modal-hide="modal-deletee-<?= $forumObj->getId() ?>"><i
                                        class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                <?= LangManager::translate("forum.manage.list.modal.definitiveDelete") ?>
                            </div>
                            <div class="modal-footer">
                                <a href="manage/deleteForum/<?= $forumObj->getId() ?>"
                                   class="btn btn-danger ml-1">
                                    <?= LangManager::translate("core.btn.delete") ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <!----MODAL EDITION CATEGORIE---->
                <div id="modal-edit-categories-<?= $category->getId() ?>" class="modal-container">
                    <div class="modal">
                        <div class="modal-header">
                            <h6><?= LangManager::translate("forum.manage.list.modal.edit") ?> <?= $category->getName() ?></h6>
                            <button type="button" data-modal-hide="modal-edit-categories-<?= $category->getId() ?>"><i
                                    class="fa-solid fa-xmark"></i></button>
                        </div>
                        <form method="post" action="manage/edit/<?= $category->getId() ?>">
                            <?php (new SecurityManager())->insertHiddenToken() ?>
                            <div class="modal-body">
                                <label for="name">Nom<span style="color: red">*</span> :</label>
                                <div class="input-group">
                                    <i class="fa-solid fa-heading"></i>
                                    <input type="text" id="name" name="name" value="<?= $category->getName() ?>"
                                           required
                                           placeholder=<?= LangManager::translate("forum.manage.list.text.commu") ?>>
                                </div>
                                <div class="icon-picker" data-id="icon" data-name="icon" data-label="Icon :"
                                     data-placeholder="Sélectionner un icon"
                                     data-value="<?= $category->getIcon() ?>"></div>
                                <label
                                    for="description"><?= LangManager::translate("forum.manage.list.text.desc") ?></label>
                                <div class="input-group">
                                    <i class="fa-solid fa-paragraph"></i>
                                    <input type="text" id="description" name="description"
                                           value="<?= $category->getDescription() ?>"
                                           placeholder="<?= LangManager::translate("forum.manage.list.text.espDesc") ?>">
                                </div>

                                <div class="private-container">
                                    <div>
                                        <label class="toggle">
                                            <p class="toggle-label"><?= LangManager::translate("forum.manage.list.text.Cat") ?></p>
                                            <input type="checkbox" class="toggle-input private-checkbox"
                                                   id="checkbox_<?= $category->getId() ?>"
                                                   name="allowedGroupsToggle" <?= $category->isRestricted() ? 'checked' : '' ?>>
                                            <div class="toggle-slider"></div>
                                        </label>
                                    </div>
                                    <div style="display: none" class="mt-2 allowedGroups"
                                         id="allowedroles_<?= $category->getId() ?>">
                                        <label
                                            for="selectBox"><?= LangManager::translate("forum.manage.list.text.role") ?></label>
                                        <div class="form-group">
                                            <select class="choices" id="selectBox" name="allowedGroups[]" multiple>
                                                <?php foreach ($ForumRoles as $ForumRole): ?>
                                                    <option
                                                        <?php foreach ($categoryModel->getAllowedRoles($category->getId()) as $allowedRoles): ?>
                                                            <?= $allowedRoles->getName() === $ForumRole->getName() ? 'selected' : '' ?>
                                                        <?php endforeach ?>
                                                        value="<?= $ForumRole->getId() ?>"><?= $ForumRole->getName() ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit"
                                        class="btn-primary"><?= LangManager::translate("core.btn.edit") ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <!----MODAL SUPPRESSION CATEGORIE---->
                <div id="modal-delete-<?= $category->getId() ?>" class="modal-container">
                    <div class="modal">
                        <div class="modal-header-danger">
                            <h6><?= LangManager::translate("forum.manage.list.modal.delete") ?> <?= $category->getName() ?></h6>
                            <button type="button" data-modal-hide="modal-delete-<?= $category->getId() ?>"><i
                                    class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body">
                            <?= LangManager::translate("forum.manage.list.modal.deleteCat") ?>
                        </div>
                        <div class="modal-footer">
                            <a href="manage/delete/<?= $category->getId() ?>" class="btn-danger">
                                <?= LangManager::translate("core.btn.delete") ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info"><?= LangManager::translate("forum.manage.list.text.alert") ?></div>
<?php endif ?>
<!--
    --MODAL AJOUT CATEGORIE--
-->
<div id="modal-add-cat" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6><?= LangManager::translate("forum.manage.list.buton.addCat") ?></h6>
            <button type="button" data-modal-hide="modal-add-cat"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="manage/add">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="modal-body">
                <label for="name">Nom<span style="color: red">*</span> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-heading"></i>
                    <input type="text" id="name" name="name" required
                           placeholder=<?= LangManager::translate("forum.manage.list.text.commu") ?>>
                </div>
                <div class="icon-picker" data-id="icon" data-name="icon" data-label="Icon :"
                     data-placeholder="Sélectionner un icon" data-value=""></div>
                <label for="description"><?= LangManager::translate("forum.manage.list.text.desc") ?></label>
                <div class="input-group">
                    <i class="fa-solid fa-paragraph"></i>
                    <input type="text" id="description" name="description"
                           placeholder="<?= LangManager::translate("forum.manage.list.text.espDesc") ?>">
                </div>

                <div>
                    <label class="toggle">
                        <p class="toggle-label"><?= LangManager::translate("forum.manage.list.text.Cat") ?></p>
                        <input type="checkbox" class="toggle-input allowedGroups" id="allowedGroupsToggle"
                               name="allowedGroupsToggle">
                        <div class="toggle-slider"></div>
                    </label>
                </div>
                <div style="display: none" class="mt-2 listAllowedGroups" id="allowedGroups">
                    <label for="selectBox"><?= LangManager::translate("forum.manage.list.text.role") ?></label>
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
            <div class="modal-footer">
                <button type="submit"
                        class="btn btn-primary ml-1"><?= LangManager::translate("core.btn.add") ?></button>
            </div>
        </form>
    </div>
</div>

<script>
    //Modal ajout catégorie
    const allowedGroupsToggleCheckbox = document.getElementById("allowedGroupsToggle");
    const allowedGroups = document.getElementById("allowedGroups");

    allowedGroupsToggleCheckbox.addEventListener("change", function () {
        if (allowedGroupsToggleCheckbox.checked) {
            allowedGroups.style.display = "block";
        } else {
            allowedGroups.style.display = "none";
        }
    });

    //Modal edition catégorie
    document.addEventListener("DOMContentLoaded", function () {
        var checkboxes = document.querySelectorAll('.private-container .private-checkbox');
        checkboxes.forEach(function (checkbox) {
            var categoryId = checkbox.id.replace('checkbox_', '');
            var contentDiv = document.getElementById('allowedroles_' + categoryId);
            checkbox.addEventListener("change", function () {
                if (checkbox.checked) {
                    contentDiv.style.display = "block";
                } else {
                    contentDiv.style.display = "none";
                }
            });
            if (checkbox.checked) {
                contentDiv.style.display = "block";
            } else {
                contentDiv.style.display = "none";
            }
        });
    });

</script>