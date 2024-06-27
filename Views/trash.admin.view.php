<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Forum\ForumResponseEntity $response*/
/* @var \CMW\Entity\Forum\ForumTopicEntity $topic*/

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>

<h3><i class="fa-solid fa-trash"></i> Corbeille</h3>

<div class="grid-2">
    <div class="card">
        <h6>Reponse / Message</h6>
        <div class="table-container">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th class="text-center">Auteur</th>
                    <th class="text-center">Raison</th>
                    <th class="text-center">Date de supression</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody class="text-center">
                <?php foreach ($responseModel->getTrashResponse() as $response) : ?>
                    <tr>
                        <td><?= $response->getUser()->getPseudo() ?></td>
                        <td><?= $response->getTrashReason() ?></td>
                        <td><?= $response->getUpdate() ?></td>
                        <td class="space-x-2">
                            <button type="button" data-modal-toggle="modal-view-<?= $response->getId() ?>">
                                <i class="text-info fa-solid fa-circle-info me-2"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-delete-<?= $response->getId() ?>">
                                <i class="text-danger fas fa-trash-alt me-2"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-restore-<?= $response->getId() ?>">
                                <i class="text-warning fa-solid fa-rotate-left"></i>
                            </button>
                        </td>
                    </tr>
                    <!--
                    ----MODAL VISUALISATION----
                    -->
                    <div id="modal-view-<?= $response->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Visualisation du message</h6>
                                <button type="button" data-modal-hide="modal-view-<?= $response->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                <p><b>Était dans : </b> <a class="link" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>forum/c/<?=$response->getResponseTopic()->getCat()->getSlug()?>/f/<?=$response->getResponseTopic()->getForum()->getSlug()?>/t/<?= $response->getResponseTopic()->getSlug() ?>/p1" target="_blank"><?= $response->getResponseTopic()->getName() ?></a></p>
                                <p><b>Messages :</b><?= $response->getContent() ?></p>
                                <p><b>Publié le :</b> <?= $response->getCreated() ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-primary" data-modal-hide="modal-view-<?= $response->getId() ?>">
                                    <?= LangManager::translate("core.btn.close") ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--
                    ----MODAL RESTAURATION----
                    -->
                    <div id="modal-restore-<?= $response->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Restauration du message</h6>
                                <button type="button" data-modal-hide="modal-restore-<?= $response->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment réactiver ce message ?
                            </div>
                            <div class="modal-footer">
                                <a href="trash/restorereply/<?= $response->getId() ?>/<?= $response->getResponseTopic()->getId() ?>" class="btn-primary">
                                    Oui
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--
                    ----MODAL SUPRESSION----
                    -->
                    <div id="modal-delete-<?= $response->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6>Supression du message</h6>
                                <button type="button" data-modal-hide="modal-delete-<?= $response->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                La supression de ce message est definitive
                            </div>
                            <div class="modal-footer">
                                <a href="trash/deletereply/<?= $response->getId() ?>" class="btn-danger">
                                    <?= LangManager::translate("core.btn.delete") ?>
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
        <h6>Topic</h6>
        <div class="table-container">
            <table class="table" id="table2">
                <thead>
                <tr>
                    <th class="text-center">Auteur</th>
                    <th class="text-center">Raison</th>
                    <th class="text-center">Date de supression</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody class="text-center">
                <?php foreach ($topicModel->getTrashTopic() as $topic) : ?>
                    <tr>
                        <td><?= $topic->getUser()->getPseudo() ?></td>
                        <td><?= $topic->getTrashReason() ?></td>
                        <td><?= $topic->getUpdate() ?></td>
                        <td class="space-x-2">
                            <button type="button" data-modal-toggle="modal-vieww-<?= $topic->getId() ?>">
                                <i class="text-info fa-solid fa-circle-info me-2"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-deletee-<?= $topic->getId() ?>">
                                <i class="text-danger fas fa-trash-alt me-2"></i>
                            </button>
                            <button type="button" data-modal-toggle="modal-restoree-<?= $topic->getId() ?>">
                                <i class="text-warning fa-solid fa-rotate-left"></i>
                            </button>
                        </td>
                    </tr>
                    <!--
                    ----MODAL VISUALISATION----
                    -->
                    <div id="modal-vieww-<?= $topic->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Visualisation du topic</h6>
                                <button type="button" data-modal-hide="modal-vieww-<?= $topic->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                <p><b>Était dans :</b> <a class="link" href="<?= $topic->getForum()->getLink() ?>" target="_blank"><?= $topic->getForum()->getName() ?></a></p>
                                <p><b>Titre : <?= $topic->getName() ?></b></p>
                                <p><b>Messages : <?= $topic->getContent() ?></b></p>
                                <p><b>Publié le :</b> <?= $topic->getCreated() ?></p>
                            </div>
                            <div class="modal-footer">
                                <button data-modal-hide="modal-vieww-<?= $topic->getId() ?>" type="button" class="btn-primary"><?= LangManager::translate("core.btn.close") ?></button>
                            </div>
                        </div>
                    </div>
                    <!--
                    ----MODAL RESTAURATION----
                    -->
                    <div id="modal-restoree-<?= $topic->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6>Restauration du topic</h6>
                                <button type="button" data-modal-hide="modal-restoree-<?= $topic->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                Etes vous sûr de vouloir réactiver ce topic et toutes les réponses qui lui sont liées ?
                            </div>
                            <div class="modal-footer">
                                <a href="trash/restoretopic/<?= $topic->getId() ?>" class="btn-primary">
                                    Oui
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--
                    ----MODAL SUPRESSION----
                    -->
                    <div id="modal-deletee-<?= $topic->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6>Suppression de <?= $topic->getName() ?></h6>
                                <button type="button" data-modal-hide="modal-deletee-<?= $topic->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                Attention !!! La suppression de ce topic supprimera également toutes les réponses qui lui sont lié !!!
                            </div>
                            <div class="modal-footer">
                                <a href="trash/deletetopic/<?= $topic->getId() ?>" class="btn-danger">
                                    <?= LangManager::translate("core.btn.delete") ?>
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
