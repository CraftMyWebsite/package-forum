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
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-trash"></i> <span class="m-lg-auto">Corbeille</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Reponse / Message</h4>
            </div>
            <div class="card-body">
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
                            <td>
                                <a type="button" data-bs-toggle="modal" data-bs-target="#view-<?= $response->getId() ?>">
                                    <i class="text-primary fa-solid fa-circle-info me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $response->getId() ?>">
                                    <i class="text-danger fas fa-trash-alt me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal" data-bs-target="#restore-<?= $response->getId() ?>">
                                    <i class="text-warning fa-solid fa-rotate-left"></i>
                                </a>
                            </td>
                        </tr>
                        <!--
                        ----MODAL VISUALISATION----
                        -->
                        <div class="modal fade text-left" id="view-<?= $response->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Visualisation du message</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p><b>Était dans : </b> <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>forum/c/<?=$response->getResponseTopic()->getCat()->getSlug()?>/f/<?=$response->getResponseTopic()->getForum()->getSlug()?>/t/<?= $response->getResponseTopic()->getSlug() ?>/p1" target="_blank"><?= $response->getResponseTopic()->getName() ?></a></p>
                                        <p><b>Messages :</b><?= $response->getContent() ?></p>
                                        <p><b>Publié le :</b> <?= $response->getCreated() ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>                             
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        ----MODAL RESTAURATION----
                        -->
                        <div class="modal fade text-left" id="restore-<?= $response->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Restauration du message</h5>
                                    </div>
                                    <div class="modal-body">
                                        Etes vous sûr de vouloir ré activer ce message ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <i class="bx bx-x d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="trash/restorereply/<?= $response->getId() ?>/<?= $response->getResponseTopic()->getId() ?>" class="btn btn-primary ml-1">
                                            <span class="">Oui</span>
                                        </a>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        ----MODAL SUPRESSION----
                        -->
                        <div class="modal fade text-left" id="delete-<?= $response->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title white" id="myModalLabel160">Supression du message</h5>
                                    </div>
                                    <div class="modal-body">
                                        La supression de ce message est definitive
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="trash/deletereply/<?= $response->getId() ?>" class="btn btn-danger ml-1">
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
                <h4>Topic</h4>
            </div>
            <div class="card-body">
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
                            <td>
                                <a type="button" data-bs-toggle="modal" data-bs-target="#vieww-<?= $topic->getId() ?>">
                                    <i class="text-primary fa-solid fa-circle-info me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal" data-bs-target="#deletee-<?= $topic->getId() ?>">
                                    <i class="text-danger fas fa-trash-alt me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal" data-bs-target="#restoree-<?= $topic->getId() ?>">
                                    <i class="text-warning fa-solid fa-rotate-left"></i>
                                </a>
                            </td>
                        </tr>
                        <!--
                        ----MODAL VISUALISATION----
                        -->
                        <div class="modal fade text-left" id="vieww-<?= $topic->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Visualisation du topic</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p><b>Était dans :</b> <a href="<?= $topic->getForum()->getLink() ?>" target="_blank"><?= $topic->getForum()->getName() ?></a></p>
                                        <p><b>Titre : <?= $topic->getName() ?></b></p>
                                        <p><b>Messages : <?= $topic->getContent() ?></b></p>
                                        <p><b>Publié le :</b> <?= $topic->getCreated() ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>                             
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        ----MODAL RESTAURATION----
                        -->
                        <div class="modal fade text-left" id="restoree-<?= $topic->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Restauration du topic</h5>
                                    </div>
                                    <div class="modal-body">
                                        Etes vous sûr de vouloir ré activer ce topic et toutes le réponse qui lui sont lié ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <i class="bx bx-x d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="trash/restoretopic/<?= $topic->getId() ?>" class="btn btn-primary ml-1">
                                            <span class="">Oui</span>
                                        </a>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        ----MODAL SUPRESSION----
                        -->
                        <div class="modal fade text-left" id="deletee-<?= $topic->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title white" id="myModalLabel160">Supression de <?= $topic->getName() ?></h5>
                                    </div>
                                    <div class="modal-body">
                                        Attention !!! La supression de ce topic suprimera également toutes les réponse qui lui sont lié !!!
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="trash/deletetopic/<?= $topic->getId() ?>" class="btn btn-danger ml-1">
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
