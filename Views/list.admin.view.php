<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Forum\PrefixModel;
use CMW\Utils\Utils;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var CMW\Model\Forum\CategoryModel $categoryModel */
/* @var CMW\Model\Forum\TopicModel $topicModel */
/* @var CMW\Model\Forum\ResponseModel $responseModel */
/* @var \CMW\Entity\Forum\TopicEntity $topic */
/* @var CMW\Controller\Forum\SettingsController $iconNotRead */
/* @var CMW\Controller\Forum\SettingsController $iconImportant */
/* @var CMW\Controller\Forum\SettingsController $iconPin */
/* @var CMW\Controller\Forum\SettingsController $iconClosed */

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span class="m-lg-auto">Gestion</span></h3>
</div>

<section>
    <div class="">
        <div class="card">
            <div class="card-header">
                <h4>Catégories et forums</h4>
            </div>
            <div class="card-body">
                <?php if ($categoryModel->getCategories()): ?>
                    <?php foreach ($categoryModel->getCategories() as $category): ?>
                        <div class="card-in-card table-responsive mb-4">
                            <table class="table-borderless table table-hover mt-1">
                                <thead>
                                <tr>
                                    <th id="categorie-<?= $category->getId() ?>"><small><i
                                                class="text-secondary fa-solid fa-circle-dot"></i></small> <?= $category->getFontAwesomeIcon() ?> <?= $category->getName() ?>
                                        -<i>
                                            <small><?= mb_strimwidth($category->getDescription(), 0, 45, '...') ?></small></i>
                                    </th>
                                    <th class="text-end">
                                        <a type="button" data-bs-toggle="modal"
                                           data-bs-target="#add-forum-<?= $category->getId() ?>">
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

                                <!--
                                	--MODAL AJOUT FORUM--
                                -->
                                <div class="modal fade " id="add-forum-<?= $category->getId() ?>" tabindex="-1"
                                     role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                         role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title white" id="myModalLabel160">Ajout d'un forum
                                                    dans <?= $category->getName() ?></h5>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" action="forums/add">
                                                    <?php (new SecurityManager())->insertHiddenToken() ?>
                                                    <input hidden type="text" name="category_id"
                                                           value="<?= $category->getId() ?>" required>
                                                    <h6>Icon :</h6>
                                                    <div class="form-group position-relative has-icon-left">
                                                        <input type="text" class="form-control" name="icon" required
                                                               placeholder="fas fa-users">
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
                                                               placeholder="Général">
                                                        <div class="form-control-icon">
                                                            <i class="fas fa-heading"></i>
                                                        </div>
                                                    </div>
                                                    <h6>Déscription :</h6>
                                                    <div class="form-group position-relative has-icon-left">
                                                        <input type="text" class="form-control" name="description"
                                                               required placeholder="Parlez de tout et de rien">
                                                        <div class="form-control-icon">
                                                            <i class="fas fa-paragraph"></i>
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
                                                    <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                                                </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--
                                    --MODAL EDITION CATEGORIE--
                                -->
                                <div class="modal fade " id="edit-categories-<?= $category->getId() ?>" tabindex="-1"
                                     role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
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

                                <!--
                                    --MODAL SUPPRESSION CATEGORIE--
                                -->
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
                                </thead>
                                <tbody>
                                <?php foreach ($forumModel->getForumByCat($category->getId()) as $forumObj): ?>
                                    <tr id="forum-<?= $forumObj->getId() ?>">
                                        <td class="ps-4 text-bold-500"><small><i
                                                    class="text-secondary fa-solid fa-turn-up fa-rotate-90"></i></small> <?= $forumObj->getFontAwesomeIcon() ?> <?= $forumObj->getName() ?>
                                            -
                                            <i><small><?= mb_strimwidth($forumObj->getDescription(), 0, 45, '...') ?></small></i>
                                        </td>
                                        <td class="text-end">
                                            <a target="_blank"
                                               href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . $forumObj->getLink() ?>"><i
                                                    class="me-3 fa-solid fa-up-right-from-square"></i></a>
                                            <a type="button" data-bs-toggle="modal"
                                               data-bs-target="#add-subforum-<?= $forumObj->getId() ?>">
                                                <i class="text-success me-3 fas fa-circle-plus"></i>
                                            </a>
                                            <a type="button" data-bs-toggle="modal"
                                               data-bs-target="#edit-forums-<?= $forumObj->getId() ?>">
                                                <i class="text-primary me-3 fas fa-edit"></i>
                                            </a>
                                            <a type="button" data-bs-toggle="modal"
                                               data-bs-target="#deletee-<?= $forumObj->getId() ?>">
                                                <i class="text-danger fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!--
                                        --LISTAGE SOUS-FORUM --
                                    -->
                                    <?php foreach ($forumModel->getSubforumByForum($forumObj->getId()) as $subForumObj): ?>
                                        <tr id="forum-<?= $subForumObj->getId() ?>">
                                            <td class="ps-5 text-bold-500"><small><i
                                                        class="text-secondary fa-solid fa-turn-up fa-rotate-90"></i></small> <?= $subForumObj->getFontAwesomeIcon() ?> <?= $subForumObj->getName() ?>
                                                -
                                                <i><small><?= mb_strimwidth($subForumObj->getDescription(), 0, 45, '...') ?></small></i>
                                            </td>
                                            <td class="text-end">
                                                <!--<a target="_blank" href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . $subForumObj->getLink() ?>"><i class="me-3 fa-solid fa-up-right-from-square"></i></a>
                                        <a type="button" data-bs-toggle="modal" data-bs-target="#edit-forums-<?= $subForumObj->getId() ?>">
                                            <i class="text-primary me-3 fas fa-edit"></i>
                                        </a>
                                        <a type="button" data-bs-toggle="modal" data-bs-target="#deletee-<?= $subForumObj->getId() ?>">
                                            <i class="text-danger fas fa-trash-alt"></i>
                                        </a>-->
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!--
                                        --MODAL AJOUT SOUS-FORUM--
                                    -->
                                    <div class="modal fade " id="add-subforum-<?= $forumObj->getId() ?>" tabindex="-1"
                                         role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                             role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h5 class="modal-title white" id="myModalLabel160">Ajout d'un
                                                        sous-forum dans <?= $forumObj->getName() ?></h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="subforums/add">
                                                        <?php (new SecurityManager())->insertHiddenToken() ?>
                                                        <input hidden type="text" name="forum_id"
                                                               value="<?= $forumObj->getId() ?>" required>
                                                        <h6>Icon :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="icon" required
                                                                   placeholder="fas fa-users">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-icons"></i>
                                                            </div>
                                                            <small class="form-text">Retrouvez la liste des icones sur
                                                                le site de <a
                                                                    href="https://fontawesome.com/search?o=r&m=free"
                                                                    target="_blank">FontAwesome.com</a></small>
                                                        </div>
                                                        <h6>Nom :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="name" required
                                                                   placeholder="Général">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-heading"></i>
                                                            </div>
                                                        </div>
                                                        <h6>Déscription :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="description"
                                                                   required placeholder="Parlez de tout et de rien">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-paragraph"></i>
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
                                                        <span
                                                            class=""><?= LangManager::translate("core.btn.add") ?></span>
                                                    </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--
                                        --MODAL EDITION FORUM--
                                    -->
                                    <div class="modal fade " id="edit-forums-<?= $forumObj->getId() ?>" tabindex="-1"
                                         role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                             role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h5 class="modal-title white" id="myModalLabel160">Édition
                                                        de <?= $forumObj->getName() ?></h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="forums/edit/<?= $forumObj->getId() ?>">
                                                        <?php (new SecurityManager())->insertHiddenToken() ?>
                                                        <h6>Changer de catégorie :</h6>
                                                        <div class="form-group">
                                                            <select class="form-select" name="category_id" required>
                                                                <?php foreach ($categoryModel->getCategories() as $category): ?>
                                                                    <option value="<?= $category->getId() ?>"
                                                                        <?= ($forumObj->getParent()->getName() === $category->getName() ? "selected" : "") ?>>
                                                                        <?= $category->getName() ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <h6>Icon :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="icon" required
                                                                   placeholder="fas fa-users"
                                                                   value="<?= $forumObj->getIcon() ?>">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-icons"></i>
                                                            </div>
                                                            <small class="form-text">Retrouvez la liste des icones sur
                                                                le site de <a
                                                                    href="https://fontawesome.com/search?o=r&m=free"
                                                                    target="_blank">FontAwesome.com</a></small>
                                                        </div>
                                                        <h6>Nom :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="name" required
                                                                   placeholder="Général"
                                                                   value="<?= $forumObj->getName() ?>">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-heading"></i>
                                                            </div>
                                                        </div>
                                                        <h6>Déscription :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="description"
                                                                   required placeholder="Parlez de tout et de rien"
                                                                   value="<?= $forumObj->getDescription() ?>">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-paragraph"></i>
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
                                                        <span
                                                            class=""><?= LangManager::translate("core.btn.edit") ?></span>
                                                    </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--
                                        --MODAL SUPRESSION FORUM--
                                    -->
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
                                                    Cette supression est définitive
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


    <div>
        <div class="card">
            <div class="card-header">
                <h4>Topics</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Forum</th>
                        <th class="text-center">Auteur</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Messages</th>
                        <th class="text-center">Affichages</th>
                        <th class="text-center"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($topicModel->getTopic() as $topic) : ?>
                        <tr>
                            <td>
                                <?= $topic->isImportant() ? "
                            <i class='<?= $iconImportant ?> fa-sm text-orange-500'></i>                           
                            " : "" ?>
                                <?= $topic->isPinned() ? "
                            <i class='<?= $iconPin ?> fa-sm text-red-600 ml-2'></i>                         
                             " : "" ?>
                                <?= $topic->isDisallowReplies() ? "
                            <i  class='<?= $iconClosed ?> fa-sm text-yellow-300 ml-2'></i>
                             " : "" ?>
                                <?php if ($topic->getPrefixId()): ?><span class="px-1 rounded-2"
                                                                          style="color: <?= $topic->getPrefixTextColor() ?>; background: <?= $topic->getPrefixColor() ?>"><?= $topic->getPrefixName() ?></span> <?php endif; ?>
                                <?= mb_strimwidth($topic->getName(), 0, 65, '...') ?>
                            </td>
                            <td><a target="_blank"
                                   href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>forum/t/<?= $topic->getSlug() ?>"><?= $topic->getForum()->getName() ?></a>
                            </td>
                            <td><img style="object-fit: fill; max-height: 32px; max-width: 32px" width="32px"
                                     height="32px"
                                     src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Public/Uploads/Users/<?= $topic->getUser()->getUserPicture()->getImageName() ?>"
                                     alt="..."><?= $topic->getUser()->getPseudo() ?></td>
                            <td><?= $topic->getCreated() ?></td>
                            <td class="text-center"><?= $responseModel->countResponseInTopic($topic->getId()) ?></td>
                            <td class="text-center"><?= $topic->countViews() ?></td>
                            <td>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#edit-prefix-<?= $topic->getId() ?>">
                                    <i class="text-success fa-solid fa-screwdriver-wrench me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#edit-prefix-">
                                    <i class="text-primary fas fa-eye me-2"></i>
                                </a>
                            </td>
                        </tr>
                        <!--
                        ----MODAL EDITION----
                        -->
                        <div class="modal fade text-left" id="edit-prefix-<?= $topic->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Gestion
                                            de <?= $topic->getName() ?></h5>
                                    </div>
                                    <div class="modal-body">
                                        <form id="modal-<?= $topic->getId() ?>" action="manage/edit" method="post">
                                            <?php (new SecurityManager())->insertHiddenToken() ?>
                                            <input type="text" name="topicId" hidden value="<?= $topic->getId() ?>">

                                            <div class="d-inline-block me-2 mb-1">
                                                <div class="form-check">
                                                    <div class="checkbox">
                                                        <input name="important" type="checkbox"
                                                               id="important<?= $topic->getId() ?>"
                                                               class="form-check-input" <?= $topic->isImportant() ? 'checked' : '' ?>>
                                                        <label for="important<?= $topic->getId() ?>"><i
                                                                class="<?= $iconImportant ?> text-yellow-300 fa-sm"></i>
                                                            Important</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-inline-block me-2 mb-1">
                                                <div class="form-check">
                                                    <div class="checkbox">
                                                        <input name="pin" type="checkbox"
                                                               id="pin<?= $topic->getId() ?>"
                                                               class="form-check-input" <?= $topic->isPinned() ? 'checked' : '' ?>>
                                                        <label for="pin<?= $topic->getId() ?>"><i
                                                                class="<?= $iconPin ?> text-red-600 fa-sm"></i> Épinglé</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-block me-2 mb-1">
                                                <div class="form-check">
                                                    <div class="checkbox">
                                                        <input name="disallow_replies" type="checkbox"
                                                               id="closed<?= $topic->getId() ?>"
                                                               class="form-check-input" <?= $topic->isDisallowReplies() ? 'checked' : '' ?>>
                                                        <label for="closed<?= $topic->getId() ?>"><i
                                                                class="<?= $iconClosed ?> text-yellow-300 fa-sm"></i>
                                                            Fermer</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-lg-6 mt-2">
                                                    <h6>Titre du topic :</h6>
                                                    <input type="text" class="form-control" name="name"
                                                           placeholder="Titre du topic" value="<?= $topic->getName() ?>"
                                                           required>
                                                </div>
                                                <div class="col-12 col-lg-6 mt-2">
                                                    <h6>Tags du topic :</h6>
                                                    <input type="text" class="form-control" name="tags"
                                                           value="<?php foreach ($topic->getTags() as $tag) {
                                                               echo "" . $tag->getContent() . ",";
                                                           } ?>" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-lg-6 mt-2">
                                                    <h6>Prefix :</h6>
                                                    <select name="prefix" class="form-select">
                                                        <option value="">Aucun</option>
                                                        <?php foreach ($prefixesModel = prefixModel::getInstance()->getPrefixes() as $prefix) : ?>
                                                            <option value="<?= $prefix->getId() ?>"
                                                                <?= ($topic->getPrefixName() === $prefix->getName() ? "selected" : "") ?>>
                                                                <?= $prefix->getName() ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-lg-6 mt-2">
                                                    <h6>Déplacer vers :</h6>
                                                    <select name="move" class="form-select">
                                                        <option value="">Aucun</option>

                                                    </select>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.save") ?></span>
                                        </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        ----MODAL SUPRESSION----
                        -->
                        <div class="modal fade text-left" id="delete-prefix-" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title white" id="myModalLabel160">Supression
                                            de </h5>
                                    </div>
                                    <div class="modal-body">
                                        Supprimer ce préfixe l'enlèvera également de tout les topics auquel il est lié.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="settings/deleteprefix/"
                                           class="btn btn-danger ml-1">
                                            <span class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                        </a>
                                    </div>
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


<!--
    --MODAL AJOUT CATEGORIE--
-->
<div class="modal fade " id="add-cat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
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


