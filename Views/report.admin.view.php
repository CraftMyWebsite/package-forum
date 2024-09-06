<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Forum\ForumPermissionRoleModel;

$title = 'Paramètres';
$description = 'desc';

/* @var \CMW\Model\Forum\ForumReportedModel $reportModel */
?>
<h3><i class="fa-solid fa-sliders"></i> Signalement</h3>

<div class="grid-2">
    <div class="card">
        <h6>Topics</h6>
        <div class="table-container">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th>Topic</th>
                    <th>Signaleur</th>
                    <th>Raison</th>
                    <th>Date</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody class="text-center">
                <?php foreach ($reportModel->getTopicsReported() as $topicReported): ?>
                    <tr>
                        <td><?= mb_strimwidth($topicReported->getTopic()->getName(), 0, 20, '...') ?></td>
                        <td><?= $topicReported->getUser()->getPseudo() ?></td>
                        <td><?= $topicReported->getReason() ?></td>
                        <td><?= $topicReported->getUpdate() ?></td>
                        <td class="space-x-2">
                            <button type="button" data-modal-toggle="modal-view-topic-<?= $topicReported->getId() ?>">
                                <i class="text-success fa-regular fa-eye"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-unreport-topic-<?= $topicReported->getId() ?>">
                                <i class="text-warning fa-regular fa-circle-xmark"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-delete-topic-<?= $topicReported->getId() ?>">
                                <i class="text-danger fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <!--
                        ----MODAL VIEW----
                        -->
                    <div id="modal-view-topic-<?= $topicReported->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Informations du topic</h6>
                                <button type="button" data-modal-hide="modal-view-topic-<?= $topicReported->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                <h6>Topic signalé :</h6>
                                <div class="flex gap-2">
                                    <div>
                                        <img class="avatar-rounded w-6 h-6" src="<?= $topicReported->getTopic()->getUser()->getUserPicture()->getImage() ?>"
                                             alt="user picture">
                                    </div>
                                    <p><?= $topicReported->getTopic()->getUser()->getPseudo() ?></p>
                                </div>
                                <p>
                                    Rôle du site : <b><?= $topicReported->getTopic()->getUser()->getHighestRole()->getName() ?></b><br>
                                    Rôle du forum : <b><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($topicReported->getTopic()->getUser()->getId())->getName() ?></b><br>
                                    Date de création : <b><?= $topicReported->getTopic()->getCreated() ?></b><br>
                                    Nom : <b><?= $topicReported->getTopic()->getName() ?></b>
                                </p>
                                <p>Contenue :</p>
                                <?= $topicReported->getTopic()->getContent() ?>
                                <hr>
                                <h6>Signaleur :</h6>
                                <div class="flex gap-2">
                                    <div>
                                        <img class="avatar-rounded w-6 h-6" src="<?= $topicReported->getUser()->getUserPicture()->getImage() ?>"
                                             alt="user picture">
                                    </div>
                                    <p><?= $topicReported->getUser()->getPseudo() ?></p>
                                </div>
                                <p>
                                    Rôle du site : <b><?= $topicReported->getUser()->getHighestRole()->getName() ?></b><br>
                                    Rôle du forum : <b><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($topicReported->getUser()->getId())->getName() ?></b><br>
                                    Raison : <b><?= $topicReported->getReason() ?></b><br>
                                    Date du signalement : <b><?= $topicReported->getUpdate() ?></b></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" data-modal-hide="modal-view-topic-<?= $topicReported->getId() ?>" class="btn btn-primary">
                                    <?= LangManager::translate('core.btn.close') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--
                    ----MODAL EDITION----
                    -->
                    <div id="modal-unreport-topic-<?= $topicReported->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Annulé ce signalement ?</h6>
                                <button type="button" data-modal-hide="modal-unreport-topic-<?= $topicReported->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                Êtes vous sûr de vouloir annulé ce signalement ?
                            </div>
                            <div class="modal-footer">
                                <a href="report/unReportTopic/<?= $topicReported->getId() ?>"
                                   class="btn-primary">
                                    Oui
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--
                    ----MODAL SUPRESSION----
                    -->
                    <div id="modal-delete-topic-<?= $topicReported->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6>Suppression
                                    du topic </h6>
                                <button type="button" data-modal-hide="modal-delete-topic-<?= $topicReported->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                Supprimer ce topic supprimera également toutes les réponses qui lui sont lié.
                                <p>Topic : <?= $topicReported->getTopic()->getName() ?></p>
                            </div>
                            <div class="modal-footer">
                                <a href="report/removeTopic/<?= $topicReported->getTopic()->getId() ?>"
                                   class="btn-danger"><?= LangManager::translate('core.btn.delete') ?>
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
        <h6>Réponses</h6>
        <div class="table-container">
            <table class="table" id="table2">
                <thead>
                <tr>
                    <th class="text-center">Auteur</th>
                    <th class="text-center">Signaleur</th>
                    <th class="text-center">Raison</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody class="text-center">
                <?php foreach ($reportModel->getResponsesReported() as $responsesReported): ?>
                    <tr>
                        <td><?= $responsesReported->getResponse()->getUser()->getPseudo() ?></td>
                        <td><?= $responsesReported->getUser()->getPseudo() ?></td>
                        <td><?= $responsesReported->getReason() ?></td>
                        <td><?= $responsesReported->getUpdate() ?></td>
                        <td class="space-x-2">
                            <button type="button" data-modal-toggle="modal-view-response-<?= $responsesReported->getId() ?>">
                                <i class="text-success fa-regular fa-eye"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-unreport-response-<?= $responsesReported->getId() ?>">
                                <i class="text-warning fa-regular fa-circle-xmark"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-delete-response-<?= $responsesReported->getId() ?>">
                                <i class="text-danger fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <!--
                            ----MODAL VIEW----
                            -->
                    <div id="modal-view-response-<?= $responsesReported->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Informations de la réponse</h6>
                                <button type="button" data-modal-hide="modal-view-response-<?= $responsesReported->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                <h6>Réponse signalé :</h6>
                                <div class="flex gap-2">
                                    <div>
                                        <img class="avatar-rounded w-6 h-6" src="<?= $responsesReported->getResponse()->getUser()->getUserPicture()->getImage() ?>"
                                             alt="user picture">
                                    </div>
                                    <p><?= $responsesReported->getResponse()->getUser()->getPseudo() ?></p>
                                </div>
                                <p>
                                    Rôle du site : <b><?= $responsesReported->getResponse()->getUser()->getHighestRole()->getName() ?></b><br>
                                    Rôle du forum : <b><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($responsesReported->getResponse()->getUser()->getId())->getName() ?></b><br>
                                    Date de création : <b><?= $responsesReported->getResponse()->getCreated() ?></b><br>
                                </p>
                                <p>Contenue :</p>
                                <?= $responsesReported->getResponse()->getContent() ?>
                                <hr>
                                <h6>Signaleur :</h6>
                                <div class="flex gap-2">
                                    <div>
                                        <img class="avatar-rounded w-6 h-6" src="<?= $responsesReported->getUser()->getUserPicture()->getImage() ?>"
                                             alt="user picture">
                                    </div>
                                    <p><?= $responsesReported->getUser()->getPseudo() ?></p>
                                </div>
                                <p>
                                    Rôle du site : <b><?= $responsesReported->getUser()->getHighestRole()->getName() ?></b><br>
                                    Rôle du forum : <b><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($responsesReported->getUser()->getId())->getName() ?></b><br>
                                    Raison : <b><?= $responsesReported->getReason() ?></b><br>
                                    Date du signalement : <b><?= $responsesReported->getUpdate() ?></b></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-primary" data-bs-dismiss="modal-view-response-<?= $responsesReported->getId() ?>">
                                    <span class=""><?= LangManager::translate('core.btn.close') ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--
                    ----MODAL EDITION----
                    -->
                    <div id="modal-unreport-response-<?= $responsesReported->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Annulé ce signalement ?</h6>
                                <button type="button" data-modal-hide="modal-unreport-response-<?= $responsesReported->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                Réponse : <?= $responsesReported->getResponse()->getContent() ?>
                            </div>
                            <div class="modal-footer">
                                <a href="report/unReportResponse/<?= $responsesReported->getId() ?>"
                                   class="btn-primary">
                                    <span class="">Oui</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--
                    ----MODAL SUPRESSION----
                    -->
                    <div id="modal-delete-response-<?= $responsesReported->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6>Supprimé cette réponse ?</h6>
                                <button type="button" data-modal-hide="modal-delete-response-<?= $responsesReported->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                <p>Êtes vous sûr ?</p>
                                <?= $responsesReported->getResponse()->getContent() ?>
                            </div>
                            <div class="modal-footer">
                                <a href="report/removeResponse/<?= $responsesReported->getResponse()->getId() ?>"
                                   class="btn-danger">
                                    <span class=""><?= LangManager::translate('core.btn.delete') ?></span>
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
