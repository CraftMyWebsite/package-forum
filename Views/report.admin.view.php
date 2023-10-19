<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Forum\ForumPermissionRoleModel;

$title = "Paramètres";
$description = "desc";

/* @var \CMW\Model\Forum\ForumReportedModel $reportModel */
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-sliders"></i> <span class="m-lg-auto">Signalement</span></h3>
</div>

<section class="row">

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Topics</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Topic</th>
                        <th class="text-center">Signaleur</th>
                        <th class="text-center">Raison</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($reportModel->getTopicsReported() as $topicReported) : ?>
                        <tr>
                            <td><?= $topicReported->getTopic()->getName() ?></td>
                            <td><?= $topicReported->getUser()->getPseudo() ?></td>
                            <td><?= $topicReported->getReason() ?></td>
                            <td><?= $topicReported->getUpdate() ?></td>
                            <td>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#view-topic-<?= $topicReported->getId() ?>">
                                    <i class="text-success fa-regular fa-eye me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#unreport-topic-<?= $topicReported->getId() ?>">
                                    <i class="text-warning fa-regular fa-circle-xmark me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#delete-topic-<?= $topicReported->getId() ?>">
                                    <i class="text-danger fas fa-trash-alt me-2"></i>
                                </a>
                            </td>
                        </tr>
                        <!--
                            ----MODAL VIEW----
                            -->
                        <div class="modal fade text-left" id="view-topic-<?= $topicReported->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Informations du topic</h5>
                                    </div>
                                    <div class="modal-body">
                                        <h6>Topic signalé :</h6>
                                        <p>Auteur : <img width="30px" height="30px" src="<?= $topicReported->getTopic()->getUser()->getUserPicture()->getImage() ?>"> <b><?= $topicReported->getTopic()->getUser()->getPseudo() ?></b><br>
                                        Rôle du site : <b><?= $topicReported->getTopic()->getUser()->getHighestRole()->getName() ?></b><br>
                                        Rôle du forum : <b><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($topicReported->getTopic()->getUser()->getId())->getName() ?></b><br>
                                            Date de création : <b><?= $topicReported->getTopic()->getCreated() ?></b><br>
                                            Nom : <b><?= $topicReported->getTopic()->getName() ?></b>
                                            </p>
                                        <p>Contenue :</p>
                                        <?= $topicReported->getTopic()->getContent() ?>
                                        <hr>
                                        <h6>Signaleur :</h6>
                                        <p>Utilisateur : <img width="30px" height="30px" src="<?= $topicReported->getUser()->getUserPicture()->getImage() ?>"> <b><?= $topicReported->getUser()->getPseudo() ?></b><br>
                                            Rôle du site : <b><?= $topicReported->getUser()->getHighestRole()->getName() ?></b><br>
                                            Rôle du forum : <b><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($topicReported->getUser()->getId())->getName() ?></b><br>
                                            Raison : <b><?= $topicReported->getReason() ?></b><br>
                                        Date du signalement : <b><?= $topicReported->getUpdate() ?></b></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="report/unReportTopic/<?= $topicReported->getId() ?>"
                                           class="btn btn-primary ml-1">
                                            <span class="">Oui</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        ----MODAL EDITION----
                        -->
                        <div class="modal fade text-left" id="unreport-topic-<?= $topicReported->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Annulé ce signalement ?</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Êtes vous sûr de vouloir annulé ce signalement ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="report/unReportTopic/<?= $topicReported->getId() ?>"
                                           class="btn btn-primary ml-1">
                                            <span class="">Oui</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        ----MODAL SUPRESSION----
                        -->
                        <div class="modal fade text-left" id="delete-topic-<?= $topicReported->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title white" id="myModalLabel160">Suppression
                                            du topic </h5>
                                    </div>
                                    <div class="modal-body">
                                        Supprimer ce topic supprimera également toutes les réponses qui lui sont lié.
                                        <p>Topic : <?= $topicReported->getTopic()->getName() ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="report/removeTopic/<?= $topicReported->getTopic()->getId() ?>"
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


    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Réponses</h4>
            </div>
            <div class="card-body">
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
                    <?php foreach ($reportModel->getResponsesReported() as $responsesReported) : ?>
                        <tr>
                            <td><?= $responsesReported->getResponse()->getUser()->getPseudo() ?></td>
                            <td><?= $responsesReported->getUser()->getPseudo() ?></td>
                            <td><?= $responsesReported->getReason() ?></td>
                            <td><?= $responsesReported->getUpdate() ?></td>
                            <td>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#view-response-<?= $responsesReported->getId() ?>">
                                    <i class="text-success fa-regular fa-eye me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#unreport-response-<?= $responsesReported->getId() ?>">
                                    <i class="text-warning fa-regular fa-circle-xmark me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#delete-response-<?= $responsesReported->getId() ?>">
                                    <i class="text-danger fas fa-trash-alt me-2"></i>
                                </a>
                            </td>
                        </tr>
                        <!--
                                ----MODAL VIEW----
                                -->
                        <div class="modal fade text-left" id="view-response-<?= $responsesReported->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Informations de la réponse</h5>
                                    </div>
                                    <div class="modal-body">
                                        <h6>Réponse signalé :</h6>
                                        <p>Auteur : <img width="30px" height="30px" src="<?= $responsesReported->getResponse()->getUser()->getUserPicture()->getImage() ?>"> <b><?= $responsesReported->getResponse()->getUser()->getPseudo() ?></b><br>
                                            Rôle du site : <b><?= $responsesReported->getResponse()->getUser()->getHighestRole()->getName() ?></b><br>
                                            Rôle du forum : <b><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($responsesReported->getResponse()->getUser()->getId())->getName() ?></b><br>
                                            Date de création : <b><?= $responsesReported->getResponse()->getCreated() ?></b><br>
                                        </p>
                                        <p>Contenue :</p>
                                        <?= $responsesReported->getResponse()->getContent() ?>
                                        <hr>
                                        <h6>Signaleur :</h6>
                                        <p>Utilisateur : <img width="30px" height="30px" src="Public/Uploads/Users/<?= $responsesReported->getUser()->getUserPicture()->getImage() ?>"> <b><?= $responsesReported->getUser()->getPseudo() ?></b><br>
                                            Rôle du site : <b><?= $responsesReported->getUser()->getHighestRole()->getName() ?></b><br>
                                            Rôle du forum : <b><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($responsesReported->getUser()->getId())->getName() ?></b><br>
                                            Raison : <b><?= $responsesReported->getReason() ?></b><br>
                                            Date du signalement : <b><?= $responsesReported->getUpdate() ?></b></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="report/unReportTopic/<?= $responsesReported->getId() ?>"
                                           class="btn btn-primary ml-1">
                                            <span class="">Oui</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        ----MODAL EDITION----
                        -->
                        <div class="modal fade text-left" id="unreport-response-<?= $responsesReported->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Annulé ce signalement ?</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Réponse : <?= $responsesReported->getResponse()->getContent() ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="report/unReportResponse/<?= $responsesReported->getId() ?>"
                                           class="btn btn-primary ml-1">
                                            <span class="">Oui</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        ----MODAL SUPRESSION----
                        -->
                        <div class="modal fade text-left" id="delete-response-<?= $responsesReported->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title white" id="myModalLabel160">Supprimé cette réponse ?</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Êtes vous sûr ?</p>
                                        <?= $responsesReported->getResponse()->getContent() ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="report/removeResponse/<?= $responsesReported->getResponse()->getId() ?>"
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