<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Forum\ForumPrefixModel;

/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var CMW\Model\Forum\ForumCategoryModel $categoryModel */
/* @var CMW\Model\Forum\ForumTopicModel $topicModel */
/* @var CMW\Model\Forum\ForumResponseModel $responseModel */
/* @var \CMW\Entity\Forum\ForumTopicEntity $topic */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotRead */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportant */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPin */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosed */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotReadColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportantColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPinColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosedColor */
/* @var \CMW\Entity\Forum\ForumPermissionRoleEntity[] $ForumRoles */

$title = LangManager::translate('forum.forum.list.title');
$description = LangManager::translate('forum.forum.list.description');
?>
<h3><i class="fa-solid fa-book"></i> Gestion des topics</h3>

<div class="table-container table-container-striped">
    <table class="table" data-load-per-page="10" id="table1" >
        <thead>
        <tr>
            <th>Nom</th>
            <th>Forum</th>
            <th>Auteur</th>
            <th>Date</th>
            <th>Messages</th>
            <th>Affichages</th>
            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($topicModel->getTopic() as $topic): ?>
            <tr>
                <td>
                    <?= $topic->isImportant() ? "
                            <i style='color: $iconImportantColor'  class='$iconImportant fa-sm'></i>                           
                            " : '' ?>
                    <?= $topic->isPinned() ? "
                            <i style='color: $iconPinColor' class='$iconPin fa-sm'></i>                         
                             " : '' ?>
                    <?= $topic->isDisallowReplies() ? "
                            <i style='color: $iconClosedColor' class='$iconClosed fa-sm'></i>
                             " : '' ?>
                    <?php if ($topic->getPrefixId()): ?><span class="px-1 rounded-2"
                                                              style="color: <?= $topic->getPrefixTextColor() ?>; background: <?= $topic->getPrefixColor() ?>"><?= $topic->getPrefixName() ?></span> <?php endif; ?>
                    <?= mb_strimwidth($topic->getName(), 0, 30, '...') ?>
                    <?php if ($topic->getIsTrash()): ?><small style="color: #d00d0d">En
                        corbeille</small><?php endif; ?>
                </td>
                <td>
                    <a target="_blank" class="link" href="<?= $topic->getLink() ?>"><?= $topic->getForum()->getName() ?></a>
                </td>
                <td class="flex items-center gap-2">
                    <img class="avatar-rounded w-6 h-6" src="<?= $topic->getUser()->getUserPicture()->getImage() ?>"
                                                         alt="user picture">
                    <?= $topic->getUser()->getPseudo() ?></td>
                <td><?= $topic->getCreated() ?></td>
                <td><?= $responseModel->countResponseInTopic($topic->getId()) ?></td>
                <td><?= $topic->countViews() ?></td>
                <td class="text-center">
                    <a type="button" data-modal-toggle="modal-edit-prefix-<?= $topic->getId() ?>">
                        <i class="text-success fa-solid fa-screwdriver-wrench"></i>
                    </a>
                </td>
            </tr>
            <!--
            ----MODAL EDITION----
            -->
            <div id="modal-edit-prefix-<?= $topic->getId() ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header">
                        <h6>Gestion de <?= $topic->getName() ?></h6>
                        <button type="button" data-modal-hide="modal-edit-prefix-<?= $topic->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form id="modal-<?= $topic->getId() ?>" method="post">
                        <?php (new SecurityManager())->insertHiddenToken() ?>
                        <input type="text" name="topicId" hidden value="<?= $topic->getId() ?>">
                        <div class="modal-body">
                            <div class="lg:flex justify-between">
                                <div>
                                    <label class="toggle">
                                        <p class="toggle-label"><i style='color: <?= $iconImportantColor ?>' class="<?= $iconImportant ?>"></i> Important</p>
                                        <input type="checkbox" class="toggle-input" name="important"
                                               id="important<?= $topic->getId() ?>" <?= $topic->isImportant() ? 'checked' : '' ?>>
                                        <div class="toggle-slider"></div>
                                    </label>
                                </div>
                                <div>
                                    <label class="toggle">
                                        <p class="toggle-label"><i style='color: <?= $iconPinColor ?>' class="<?= $iconPin ?>"></i> Épinglé</p>
                                        <input type="checkbox" class="toggle-input" name="pin"
                                               id="pin<?= $topic->getId() ?>" <?= $topic->isPinned() ? 'checked' : '' ?>>
                                        <div class="toggle-slider"></div>
                                    </label>
                                </div>
                                <div>
                                    <label class="toggle">
                                        <p class="toggle-label"><i style='color: <?= $iconClosedColor ?>' class="<?= $iconClosed ?>"></i> Fermer</p>
                                        <input type="checkbox" class="toggle-input" name="disallow_replies"
                                               id="closed<?= $topic->getId() ?>" <?= $topic->isDisallowReplies() ? 'checked' : '' ?>>
                                        <div class="toggle-slider"></div>
                                    </label>
                                </div>
                            </div>
                            <label for="name">Titre du topic :</label>
                            <input type="text" id="name" class="input" name="name"
                                   placeholder="Titre du topic" value="<?= $topic->getName() ?>"
                                   required>
                            <label for="default-input">Tags du topic :</label>
                            <input type="text" id="default-input" class="input" name="tags"
                                   value="<?php foreach ($topic->getTags() as $tag) {
        echo '' . $tag->getContent() . ',';
    } ?>">
                            <label for="prefix">Prefix :</label>
                            <select id="prefix" name="prefix" class="form-select">
                                <option value="">Aucun</option>
                                <?php foreach ($prefixesModel = ForumPrefixModel::getInstance()->getPrefixes() as $prefix): ?>
                                    <option value="<?= $prefix->getId() ?>"
                                        <?= ($topic->getPrefixName() === $prefix->getName() ? 'selected' : '') ?>>
                                        <?= $prefix->getName() ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="move">Déplacer vers :</label>
                            <select id="move" name="move" class="form-select">
                                <?php foreach ($categoryModel->getCategories() as $cat): ?>
                                    <option disabled>──── <?= $cat->getName() ?> ────</option>
                                    <?php foreach ($forumModel->getForumByCat($cat->getId()) as $forumObj): ?>
                                        <option value="<?= $forumObj->getId() ?>" <?= ($forumObj->getName() === $topic->getForum()->getName() ? 'selected' : '') ?>><?= $forumObj->getName() ?></option>
                                        <?php foreach ($forumModel->getSubsForums($forumObj->getId()) as $subForum): ?>
                                            <option value="<?= $subForum['subforum']->getId() ?>" <?= ($subForum['subforum']->getName() === $topic->getForum()->getName() ? 'selected' : '') ?>> <?= str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $subForum['depth']) ?> ↪ <?= $subForum['subforum']->getName() ?></option>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn-primary">
                                <?= LangManager::translate('core.btn.save') ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>