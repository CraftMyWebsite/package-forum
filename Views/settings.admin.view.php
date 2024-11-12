<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = 'Paramètres';
$description = 'desc';

/* @var CMW\Controller\Forum\ForumSettingsController $needConnectUrl */
/* @var CMW\Controller\Forum\ForumSettingsController $needConnectText */
/* @var CMW\Controller\Forum\ForumSettingsController $blinkResponse */
/* @var CMW\Controller\Forum\ForumSettingsController $responsePerPage */
/* @var CMW\Controller\Forum\ForumSettingsController $topicPerPage */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotRead */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportant */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPin */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosed */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotReadColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportantColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPinColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosedColor */
/* @var CMW\Model\Forum\ForumPrefixModel $prefixesModel */
/* @var CMW\Model\Forum\ForumFeedbackModel $feedbackModel */
?>

<h3><i class="fa-solid fa-sliders"></i> Paramètres</h3>

<div class="grid-2">
    <div class="card">
        <h6>Général</h6>
        <form action="settings/general" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="grid-2">
                <div>
                    <label for="topicPerPage">Topics par page :</label>
                    <input type="number" class="input" id="topicPerPage" name="topicPerPage" value="<?= $topicPerPage ?>"
                           required>
                </div>
                <div>
                    <label for="responsePerPage">Réponses par page :</label>
                    <input type="number" class="input" id="responsePerPage" name="responsePerPage"
                           value="<?= $responsePerPage ?>"
                           required>
                </div>
            </div>
            <div class="grid-2 mt-4 mb-4">
                <div>
                    <label class="toggle">
                        <p class="toggle-label">Doit être connecté pour voir les URL</p>
                        <input type="checkbox" class="toggle-input" id="needConnectUrl" name="needConnectUrl" <?= $needConnectUrl ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                    </label>
                </div>
                <div>
                    <label class="toggle">
                        <p class="toggle-label">Effet clignotement sur les réponses</p>
                        <input type="checkbox" class="toggle-input" id="blinkResponse" name="blinkResponse" <?= $blinkResponse ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                    </label>
                </div>
            </div>
            <label for="needConnectText">Texte pour les non connecté :</label>
            <textarea class="tinymce" id="needConnectText" name="needConnectText" data-tiny-height="100"><?= $needConnectText ?></textarea>
            <div class="mt-4">
                <button type="submit" class="btn-center btn-primary">
                    <?= LangManager::translate('core.btn.save') ?>
                </button>
            </div>
        </form>
    </div>

    <form action="settings/applyicons" method="post">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div class="card">
            <h6>Icônes :</h6>
            <div class="grid-2">
                <div>
                    <h6>Non lu :</h6>
                    <div class="text-center mb-2">
                        <i style="font-size : 3rem; color: <?= $iconNotReadColor ?>" class="<?= $iconNotRead ?>"></i>
                    </div>
                    <div class="icon-picker" data-id="icon_notRead" data-name="icon_notRead" data-label="" data-placeholder="Sélectionner un icon" data-value="<?= $iconNotRead ?>"></div>
                    <input type="color" class="w-full" id="icon_notRead_color" name="icon_notRead_color" value="<?= $iconNotReadColor ?>" required>
                </div>
                <div>
                    <h6>Important :</h6>
                    <div class="text-center mb-2">
                        <i style="font-size : 3rem; color: <?= $iconImportantColor ?>" class="<?= $iconImportant ?>"></i>
                    </div>
                    <div class="icon-picker" data-id="icon_important" data-name="icon_important" data-label="" data-placeholder="Sélectionner un icon" data-value="<?= $iconImportant ?>"></div>
                    <input type="color" class="w-full" id="icon_important_color" name="icon_important_color" value="<?= $iconImportantColor ?>" required>
                </div>
                <div>
                    <h6>Épinglé :</h6>
                    <div class="text-center mb-2">
                        <i style="font-size : 3rem; color: <?= $iconPinColor ?>" class="<?= $iconPin ?>"></i>
                    </div>
                    <div class="icon-picker" data-id="icon_pin" data-name="icon_pin" data-label="" data-placeholder="Sélectionner un icon" data-value="<?= $iconPin ?>"></div>
                    <input type="color" class="w-full" id="icon_pin_color" name="icon_pin_color" value="<?= $iconPinColor ?>" required>
                </div>
                <div>
                    <h6>Clos :</h6>
                    <div class="text-center mb-2">
                        <i style="font-size : 3rem; color: <?= $iconClosedColor ?>" class="<?= $iconClosed ?>"></i>
                    </div>
                    <div class="icon-picker" data-id="icon_closed" data-name="icon_closed" data-label="" data-placeholder="Sélectionner un icon" data-value="<?= $iconClosed ?>"></div>
                    <input type="color" class="w-full" id="icon_closed_color" name="icon_closed_color" value="<?= $iconClosedColor ?>" required>
                </div>
            </div>
                <div class="mt-4">
                    <button type="submit" class="btn-center btn-primary">
                        <?= LangManager::translate('core.btn.save') ?>
                    </button>
                </div>
            </div>
    </form>
</div>

<div class="grid-2 mt-4">
    <div class="card">
        <div class="lg:flex justify-between">
            <h6>Réactions</h6>
            <button type="button" data-modal-toggle="modal-add-reaction" class="btn-primary"><?= LangManager::translate('core.btn.add') ?></button>
        </div>
        <div class="table-container">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($feedbackModel->getFeedbacks() as $feedback): ?>
                    <tr>
                        <td class="text-center mx-auto"><img alt="..." width="32px" src="<?= $feedback->getImage() ?>"></td>
                        <td><?= $feedback->getName() ?></td>
                        <td class="text-center space-x-2">
                            <button type="button" data-modal-toggle="modal-edit-feedback-<?= $feedback->getId() ?>">
                                <i class="text-info fas fa-edit"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-delete-feedback-<?= $feedback->getId() ?>">
                                <i class="text-danger fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <!--
                    ----MODAL EDITION----
                    -->
                    <div id="modal-edit-feedback-<?= $feedback->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Édition de <?= $feedback->getName() ?></h6>
                                <button type="button" data-modal-hide="modal-edit-feedback-<?= $feedback->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <form action="settings/editreaction" method="post"
                                  enctype="multipart/form-data">
                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <div class="modal-body">
                                    <input name="id" hidden value="<?= $feedback->getId() ?>">
                                    <label for="name">Nom :</label>
                                    <input type="text" class="input" id="name" name="name"
                                           value="<?= $feedback->getName() ?>">
                                    <input type="file" class="" name="image" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn-primary">
                                        <span class=""><?= LangManager::translate('core.btn.save') ?></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--
                    ----MODAL SUPRESSION----
                    -->
                    <div id="modal-delete-feedback-<?= $feedback->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6>Supression
                                    de <?= $feedback->getName() ?></h6>
                                <button type="button" data-modal-hide="modal-delete-feedback-<?= $feedback->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                Supprimer cette réaction supprimera aussi tout les like donner avec cette
                                réaction !
                            </div>
                            <div class="modal-footer">
                                <a href="settings/deletereaction/<?= $feedback->getId() ?>"
                                   class="btn-danger">
                                    <?= LangManager::translate('core.btn.delete') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="lg:flex justify-between">
            <h6>Prefix</h6>
            <button type="button" data-modal-toggle="modal-add-prefix" class="btn-primary"><?= LangManager::translate('core.btn.add') ?></button>
        </div>

        <div class="table-container">
            <table class="table" id="table2">
                <thead>
                <tr>
                    <th class="text-center">Nom</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody class="text-center">
                <?php foreach ($prefixesModel->getPrefixes() as $prefix): ?>
                    <tr>
                        <td><span class="px-2 rounded-2"
                                  style="color: <?= $prefix->getTextColor() ?>; background: <?= $prefix->getColor() ?>"><?= $prefix->getName() ?></span>
                        </td>
                        <td><?= $prefix->getDescription() ?></td>
                        <td class="space-x-2">
                            <button type="button" data-modal-toggle="modal-edit-prefix-<?= $prefix->getId() ?>">
                                <i class="text-info fas fa-edit"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-delete-prefix-<?= $prefix->getId() ?>">
                                <i class="text-danger fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <!--
                    ----MODAL EDITION----
                    -->
                    <div id="modal-edit-prefix-<?= $prefix->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Édition de <?= $prefix->getName() ?></h6>
                                <button type="button" data-modal-hide="modal-edit-prefix-<?= $prefix->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <form action="settings/editprefix" method="post">
                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <div class="modal-body">
                                    <input name="prefixId" hidden value="<?= $prefix->getId() ?>">
                                    <div class="grid-2">
                                        <div>
                                            <label>Nom :</label>
                                            <input type="text" class="input" name="prefixName"
                                                   value="<?= $prefix->getName() ?>" placeholder="Annonce"
                                                   required>
                                        </div>
                                        <div>
                                            <label>Description :</label>
                                            <input type="text" class="input" name="prefixDescription"
                                                   value="<?= $prefix->getDescription() ?>" required>
                                        </div>
                                        <div>
                                            <label>Couleur du texte :</label>
                                            <input type="color" class="w-full" name="prefixTextColor"
                                                   value="<?= $prefix->getTextColor() ?>" required>
                                        </div>
                                        <div>
                                            <label>Couleur du fond :</label>
                                            <input type="color" class="w-full" name="prefixColor"
                                                   value="<?= $prefix->getColor() ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn-primary">
                                        <?= LangManager::translate('core.btn.save') ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--
                    ----MODAL SUPRESSION----
                    -->
                    <div id="modal-delete-prefix-<?= $prefix->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6>Supression
                                    de <?= $prefix->getName() ?></h6>
                                <button type="button" data-modal-hide="modal-delete-prefix-<?= $prefix->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                Supprimer ce préfixe l'enlèvera également de tout les topics auquel il est lié.
                            </div>
                            <div class="modal-footer">
                                <a href="settings/deleteprefix/<?= $prefix->getId() ?>"
                                   class="btn-danger">
                                    <?= LangManager::translate('core.btn.delete') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!--
            ----MODAL AJOUT PREFIX ----
            -->
<div id="modal-add-prefix" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6>Nouveau prefix</h6>
            <button type="button" data-modal-hide="modal-add-prefix"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="settings/addprefix" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <div class="grid-2">
                    <div>
                        <label for="prefixName">Nom :</label>
                        <input type="text" class="input" id="prefixName" name="prefixName" placeholder="Annonce"
                               required>
                    </div>
                    <div>
                        <label for="prefixDescription">Description :</label>
                        <input type="text" class="input" id="prefixDescription" name="prefixDescription" required>
                    </div>
                    <div>
                        <label for="prefixTextColor">Couleur du texte :</label>
                        <input type="color" class="w-full" id="prefixTextColor" name="prefixTextColor" required>
                    </div>
                    <div>
                        <label for="prefixColor">Couleur du fond :</label>
                        <input type="color" class="w-full" id="prefixColor" name="prefixColor" value="white" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary">
                    <?= LangManager::translate('core.btn.add') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!--
            ----MODAL AJOUT REACT ----
            -->
<div id="modal-add-reaction" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6>Nouvelle réaction</h6>
            <button type="button" data-modal-hide="modal-add-reaction"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="sendImage" action="settings/addreaction" method="post"
              enctype="multipart/form-data">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <label for="name">Nom :</label>
                <input type="text" class="input" id="name" name="name">
                <div class="drop-img-area" data-input-name="image"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary">
                    <span class=""><?= LangManager::translate('core.btn.add') ?></span>
                </button>
            </div>
        </form>
    </div>
</div>