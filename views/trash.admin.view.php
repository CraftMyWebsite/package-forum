<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span class="m-lg-auto">Corbeille</span></h3>
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
                        <th class="text-center">Date de supression</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php foreach ($responseModel->getTrashResponse() as $response) : ?>
                        <tr>
                            <td><?= $response->getUser()->getPseudo() ?></td>
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
                                        <p><b>Était dans :</b> <a href="/forum/t/<?= $response->getResponseTopic()->getSlug() ?>" target="_blank"><?= $response->getResponseTopic()->getName() ?></a></p>
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
                                        <a href="trash/restorereply/<?= $response->getId() ?>" class="btn btn-primary ml-1">
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
                <h4>Titre</h4>
            </div>
            <div class="card-body">
               
            </div>
        </div>
    </div>
</section>
