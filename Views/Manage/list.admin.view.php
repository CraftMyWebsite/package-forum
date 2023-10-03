<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Forum\ForumPrefixModel;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var CMW\Model\Forum\ForumCategoryModel $categoryModel */
/* @var \CMW\Entity\Forum\ForumPermissionRoleEntity[] $ForumRoles */

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span class="m-lg-auto">Catégories et forums</span></h3>
</div>

<section>
    <div class="">
        <div class="card">
            <div class="card-body">
                <?php if ($categoryModel->getCategories()): ?>
                    <?php foreach ($categoryModel->getCategories() as $category): ?>
                        <div class="card-in-card table-responsive mb-4">
                            <table class="table-borderless table table-hover mt-1">
                                <thead>
                                <tr>
                                    <th id="categorie-<?= $category->getId() ?>">
                                        <small><i class="text-secondary fa-solid fa-circle-dot"></i></small>
                                        <?= $category->getFontAwesomeIcon() ?> <?= $category->getName() ?>
                                        <?php if ($category->isRestricted()): ?><small style="color: #af1a1a">Restreint
                                            <i data-bs-toggle="tooltip" title="<?php foreach ($categoryModel->getAllowedRoles($category->getId()) as $allowedRoles): ?> - <?= $allowedRoles->getName() ?> <?php endforeach ?>" class="fa-sharp fa-solid fa-circle-info"></i></small>
                                        <?php endif; ?> -
                                        <i><small><?= mb_strimwidth($category->getDescription(), 0, 45, '...') ?></small></i>
                                    </th>
                                    <th class="text-end">
                                        <a target="_blank"
                                           href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] ."/forum/c/". $category->getSlug() ?>"><i
                                                class="me-3 fa-solid fa-up-right-from-square"></i></a>
                                        <a href="manage/addForum/<?= $category->getId() ?>">
                                            <i class="text-success me-3 fa-solid fa-circle-plus"></i>
                                        </a>
                                        <a type="button" data-bs-toggle="modal"
                                           data-bs-target="#edit-categories-<?= $category->getId() ?>">
                                            <i class="text-primary me-3 fas fa-edit"></i>
                                        </a>
                                        <a type="button" data-bs-toggle="modal"
                                           data-bs-target="#delete-<?= $category->getId() ?>">
                                            <i class="text-danger fas fa-trash-alt"></i>
                                        </a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($forumModel->getForumByCat($category->getId()) as $forumObj): ?>
                                    <tr id="forum-<?= $forumObj->getId() ?>">
                                        <td class="ps-4 text-bold-500">
                                            <small><i class="text-secondary fa-solid fa-turn-up fa-rotate-90"></i></small>
                                            <?= $forumObj->getFontAwesomeIcon() ?>
                                            <?= $forumObj->getName() ?>
                                            <?php if ($forumObj->isRestricted()): ?><small style="color: #af1a1a">Restreint
                                                <i data-bs-toggle="tooltip" title="<?php foreach ($forumModel->getAllowedRoles($forumObj->getId()) as $allowedRoles): ?> - <?= $allowedRoles->getName() ?> <?php endforeach ?>" class="fa-sharp fa-solid fa-circle-info"></i></small>
                                            <?php endif; ?>
                                            -
                                            <i><small><?= mb_strimwidth($forumObj->getDescription(), 0, 45, '...') ?></small></i>
                                        </td>
                                        <td class="text-end">
                                            <a target="_blank"
                                               href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] ."/forum/c/". $category->getSlug() ."/f/". $forumObj->getLink() ?>"><i
                                                    class="me-3 fa-solid fa-up-right-from-square"></i></a>
                                            <a href="manage/addSubForum/<?= $forumObj->getId() ?>">
                                                <i class="text-success me-3 fas fa-circle-plus"></i>
                                            </a>
                                            <a href="manage/editForum/<?= $forumObj->getId() ?>">
                                                <i class="text-primary me-3 fas fa-edit"></i>
                                            </a>
                                            <a type="button" data-bs-toggle="modal"
                                               data-bs-target="#deletee-<?= $forumObj->getId() ?>">
                                                <i class="text-danger fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <!----LISTAGE SOUS-FORUM ---->
                                    <!--TODO : Besoin d'être améliorer -->
                                    <?php foreach ($forumModel->getSubsForums($forumObj->getId()) as $subForum): ?>
                                        <tr>
                                            <td style="padding-left: <?= 1 + $subForum["depth"] * 2 ?>rem"
                                                class="text-bold-500">
                                                <i class="text-secondary fa-solid fa-turn-up fa-rotate-90"></i>
                                                <?= $subForum["subforum"]->getFontAwesomeIcon() ?>
                                                <?= $subForum["subforum"]->getName() ?>
                                                <?php if ($subForum["subforum"]->isRestricted()): ?><small style="color: #af1a1a">Restreint
                                                    <i data-bs-toggle="tooltip" title="<?php foreach ($forumModel->getAllowedRoles($subForum["subforum"]->getId()) as $allowedRoles): ?> - <?= $allowedRoles->getName() ?> <?php endforeach ?>" class="fa-sharp fa-solid fa-circle-info"></i></small>
                                                <?php endif; ?>
                                                -
                                                <i><small><?= mb_strimwidth($subForum["subforum"]->getDescription(), 0, 45, '...') ?></small></i>
                                            </td>
                                            <td class="text-end">
                                                <a target="_blank"
                                                   href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] ."/forum/c/". $category->getSlug() ."/f/". $subForum["subforum"]->getLink() ?>"><i
                                                        class="me-3 fa-solid fa-up-right-from-square"></i></a>
                                                <a href="manage/addSubForum/<?= $subForum["subforum"]->getId() ?>">
                                                    <i class="text-success me-3 fas fa-circle-plus"></i>
                                                </a>
                                                <a href="manage/editForum/<?= $subForum["subforum"]->getId() ?>">
                                                    <i class="text-primary me-3 fas fa-edit"></i>
                                                </a>
                                                <a type="button" data-bs-toggle="modal"
                                                   data-bs-target="#delete-sub-<?= $subForum["subforum"]->getId() ?>">
                                                    <i class="text-danger fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <!----MODAL SUPRESSION SOUS FORUM---->
                                        <div class="modal fade text-left"
                                             id="delete-sub-<?= $subForum["subforum"]->getId() ?>"
                                             tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                                 role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title white" id="myModalLabel160">Supression de
                                                            : <?= $subForum["subforum"]->getName() ?></h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        Cette suppression est définitive et entraine la suppression de ces sous-forum et des topics qui lui sont lié
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light-secondary"
                                                                data-bs-dismiss="modal">
                                                            <i class="bx bx-x"></i>
                                                            <span
                                                                class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                        </button>
                                                        <a href="forums/delete/<?= $subForum["subforum"]->getId() ?>"
                                                           class="btn btn-danger ml-1">
                                                            <i class="bx bx-check"></i>
                                                            <span
                                                                class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <!----MODAL SUPRESSION FORUM---->
                                    <div class="modal fade text-left" id="deletee-<?= $forumObj->getId() ?>"
                                         tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                             role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title white" id="myModalLabel160">Supression de
                                                        : <?= $forumObj->getName() ?></h5>
                                                </div>
                                                <div class="modal-body">
                                                    Cette suppression est définitive et entraine la suppression de ces sous-forum et des topics qui leurs sont lié
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light-secondary"
                                                            data-bs-dismiss="modal">
                                                        <i class="bx bx-x"></i>
                                                        <span
                                                            class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                    </button>
                                                    <a href="forums/delete/<?= $forumObj->getId() ?>"
                                                       class="btn btn-danger ml-1">
                                                        <i class="bx bx-check"></i>
                                                        <span
                                                            class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!----MODAL EDITION CATEGORIE---->
                        <div class="modal fade " id="edit-categories-<?= $category->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered"
                                 role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Édition
                                            de <?= $category->getName() ?></h5>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="categories/edit/<?= $category->getId() ?>">
                                            <?php (new SecurityManager())->insertHiddenToken() ?>
                                            <h6>Icon :</h6>
                                            <div class="form-group position-relative has-icon-left">
                                                <input type="text" class="form-control" name="icon" required
                                                       placeholder="fas fa-users"
                                                       value="<?= $category->getIcon() ?>">
                                                <div class="form-control-icon">
                                                    <i class="fas fa-icons"></i>
                                                </div>
                                                <small class="form-text">Retrouvez la liste des icones sur le
                                                    site de <a href="https://fontawesome.com/search?o=r&m=free"
                                                               target="_blank">FontAwesome.com</a></small>
                                            </div>
                                            <h6>Nom :</h6>
                                            <div class="form-group position-relative has-icon-left">
                                                <input type="text" class="form-control" name="name" required
                                                       placeholder="Général"
                                                       value="<?= $category->getName() ?>">
                                                <div class="form-control-icon">
                                                    <i class="fas fa-heading"></i>
                                                </div>
                                            </div>
                                            <h6>Déscription :</h6>
                                            <div class="form-group position-relative has-icon-left">
                                                <input type="text" class="form-control" name="description"
                                                       required placeholder="Parlez de tout et de rien"
                                                       value="<?= $category->getDescription() ?>">
                                                <div class="form-control-icon">
                                                    <i class="fas fa-paragraph"></i>
                                                </div>
                                            </div>
                                            <div class="private-container">
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input private-checkbox" type="checkbox" id="checkbox_<?= $category->getId() ?>"
                                                           name="allowedGroupsToggle" <?= $category->isRestricted() ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="checkbox_<?= $category->getId() ?>"><h6>Catégorie privé</h6></label>
                                                </div>
                                                <div class="mt-2 allowedGroups" id="allowedroles_<?= $category->getId() ?>">
                                                    <h6>Rôle autorisé :</h6>
                                                    <div class="form-group">
                                                        <select class="choices form-select" id="selectBox" name="allowedGroups[]" multiple>
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
                                        <button type="button" class="btn btn-light-secondary"
                                                data-bs-dismiss="modal">
                                            <i class="bx bx-x"></i>
                                            <span
                                                class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <button type="submit" class="btn btn-primary ml-1">
                                            <i class="bx bx-check"></i>
                                            <span class=""><?= LangManager::translate("core.btn.edit") ?></span>
                                        </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!----MODAL SUPPRESSION CATEGORIE---->
                        <div class="modal fade text-left" id="delete-<?= $category->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                 role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title white" id="myModalLabel160">Supression de
                                            : <?= $category->getName() ?></h5>
                                    </div>
                                    <div class="modal-body">
                                        Cette supression est définitive
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary"
                                                data-bs-dismiss="modal">
                                            <i class="bx bx-x"></i>
                                            <span
                                                class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="categories/delete/<?= $category->getId() ?>"
                                           class="btn btn-danger ml-1">
                                            <i class="bx bx-check"></i>
                                            <span
                                                class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">Merci de créer une catégorie pour commencer à utiliser le Forum</div>
                <?php endif ?>
                <div class="divider">
                    <a type="button" data-bs-toggle="modal" data-bs-target="#add-cat">
                        <div class="divider-text"><i class="fa-solid fa-circle-plus"></i> Ajouter une catégorie</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!--
    --MODAL AJOUT CATEGORIE--
-->
<div class="modal fade " id="add-cat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title white"
                    id="myModalLabel160"><?= LangManager::translate("wiki.title.add_category") ?></h5>
            </div>
            <div class="modal-body">
                <form method="post" action="categories/add">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <h6>Icon :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="icon" required placeholder="fas fa-users">
                        <div class="form-control-icon">
                            <i class="fas fa-icons"></i>
                        </div>
                        <small class="form-text">Retrouvez la liste des icones sur le site de <a
                                href="https://fontawesome.com/search?o=r&m=free"
                                target="_blank">FontAwesome.com</a></small>
                    </div>
                    <h6>Nom :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="name" required placeholder="Communauté">
                        <div class="form-control-icon">
                            <i class="fas fa-heading"></i>
                        </div>
                    </div>
                    <h6>Description :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="description" required
                               placeholder="L'éspace communautaire">
                        <div class="form-control-icon">
                            <i class="fas fa-paragraph"></i>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input allowedGroups" type="checkbox" id="allowedGroupsToggle"
                               name="allowedGroupsToggle">
                        <label class="form-check-label" for="allowedGroupsToggle"><h6>Catégorie privé</h6></label>
                    </div>
                    <div style="display: none" class="mt-2 listAllowedGroups" id="allowedGroups">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x"></i>
                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                </button>
                <button type="submit" class="btn btn-primary ml-1">
                    <i class="bx bx-check"></i>
                    <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                </button>
                </form>
            </div>
        </div>
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