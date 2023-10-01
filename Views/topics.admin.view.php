<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Forum\ForumPrefixModel;
use CMW\Manager\Security\SecurityManager;

/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var CMW\Model\Forum\ForumCategoryModel $categoryModel */
/* @var CMW\Model\Forum\ForumTopicModel $topicModel */
/* @var CMW\Model\Forum\ForumResponseModel $responseModel */
/* @var \CMW\Entity\Forum\ForumTopicEntity $topic */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotRead */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportant */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPin */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosed */
/* @var \CMW\Entity\Forum\ForumPermissionRoleEntity[] $ForumRoles */

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span class="m-lg-auto">Gestion des topics</span></h3>
</div>

<section>
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
                                <?php if ($topic->getIsTrash()) : ?><small style="color: #d00d0d">En
                                    corbeille</small><?php endif; ?>
                            </td>
                            <td class="text-center"><a target="_blank"
                                                       href="<?= $topic->getLink() ?>"><?= $topic->getForum()->getName() ?></a>
                            </td>
                            <td class="text-center"><img style="object-fit: fill; max-height: 32px; max-width: 32px"
                                                         width="32px"
                                                         height="32px"
                                                         src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Public/Uploads/Users/<?= $topic->getUser()->getUserPicture()->getImageName() ?>"
                                                         alt="..."><?= $topic->getUser()->getPseudo() ?></td>
                            <td class="text-center"><?= $topic->getCreated() ?></td>
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
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
                                                        <?php foreach ($prefixesModel = ForumPrefixModel::getInstance()->getPrefixes() as $prefix) : ?>
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
                                                        <?php foreach ($categoryModel->getCategories() as $cat): ?>
                                                            <option disabled>──── <?= $cat->getName() ?> ────</option>
                                                            <?php foreach ($forumModel->getForumByCat($cat->getId()) as $forumObject): ?>
                                                                <option
                                                                    value="<?= $forumObject->getId() ?>" <?= ($forumObject->getName() === $topic->getForum()->getName() ? "selected" : "") ?>><?= $forumObject->getName() ?></option>
                                                                <?php foreach ($forumModel->getSubforumByForum($forumObject->getId()) as $subForumObject): ?>
                                                                    <option value="<?= $subForumObject->getId() ?>">
                                                                        ↪ <?= $subForumObject->getName() ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endforeach; ?>
                                                        <?php endforeach; ?>
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
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>