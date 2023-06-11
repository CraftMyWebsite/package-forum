<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "Paramètres";
$description = "desc";

/* @var CMW\Controller\Forum\SettingsController $iconNotRead */
/* @var CMW\Controller\Forum\SettingsController $iconImportant */
/* @var CMW\Controller\Forum\SettingsController $iconPin */
/* @var CMW\Controller\Forum\SettingsController $iconClosed */
/* @var CMW\Model\Forum\PrefixModel $prefixesModel */
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-sliders"></i> <span class="m-lg-auto">Paramètres</span></h3>
</div>

<div class="">
    <form action="settings/applyicons" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <div class="card">
            <div class="card-header">
                <h4>Icônes :</h4>
            </div>
            <div class="card-body row">
                <div class="col-12 col-lg-3 mb-4">
                    <div class="card-in-card me-2 p-3">
                        <h6>Non lue :</h6>
                        <div class="text-center mb-2">
                            <i style="font-size : 3rem;" class="<?= $iconNotRead ?>"></i>
                        </div>
                        <input type="text" class="form-control" name="icon_notRead" value="<?= $iconNotRead ?>"
                               required>
                    </div>
                </div>
                <div class="col-12 col-lg-3 mb-4">
                    <div class="card-in-card me-2 p-3">
                        <h6>Important :</h6>
                        <div class="text-center mb-2">
                            <i style="font-size : 3rem;" class="<?= $iconImportant ?>"></i>
                        </div>
                        <input type="text" class="form-control" name="icon_important" value="<?= $iconImportant ?>"
                               required>
                    </div>
                </div>
                <div class="col-12 col-lg-3 mb-4">
                    <div class="card-in-card me-2 p-3">
                        <h6>Épingler :</h6>
                        <div class="text-center mb-2">
                            <i style="font-size : 3rem;" class="<?= $iconPin ?>"></i>
                        </div>
                        <input type="text" class="form-control" name="icon_pin" value="<?= $iconPin ?>" required>
                    </div>
                </div>
                <div class="col-12 col-lg-3 mb-4">
                    <div class="card-in-card me-2 p-3">
                        <h6>Clos :</h6>
                        <div class="text-center mb-2">
                            <i style="font-size : 3rem;" class="<?= $iconClosed ?>"></i>
                        </div>
                        <input type="text" class="form-control" name="icon_closed" value="<?= $iconClosed ?>"
                               required>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <?= LangManager::translate("core.btn.save") ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<section class="row">

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Réactions</h4>
            </div>
            <div class="card-body">

            </div>
        </div>
    </div>


    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Prefix</h4>
            </div>
            <div class="position-absolute end-0">
                <a type="button" data-bs-toggle="modal"
                   data-bs-target="#add-prefix"
                   class="text-bg-primary rounded-2 py-1 px-2"><?= LangManager::translate("core.btn.add") ?></a>
            </div>
            <!--
            ----MODAL AJOUT ----
            -->
            <div class="modal fade text-left" id="add-prefix" tabindex="-1"
                 role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title white" id="myModalLabel160">Nouveau prefix</h5>
                        </div>
                        <div class="modal-body">
                            <form action="settings/addprefix" method="post">
                                <?php (new SecurityManager())->insertHiddenToken() ?>
                                <div class="row">
                                    <div class="col-12 col-lg-6 mt-2">
                                        <h6>Nom :</h6>
                                        <input type="text" class="form-control" name="prefixName" placeholder="Annonce"
                                               required>
                                    </div>
                                    <div class="col-12 col-lg-6 mt-2">
                                        <h6>Description :</h6>
                                        <input type="text" class="form-control" name="prefixDescription" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-lg-6 mt-2">
                                        <h6>Couleur du texte :</h6>
                                        <input type="color" class="form-control" name="prefixTextColor" required>
                                    </div>
                                    <div class="col-12 col-lg-6 mt-2">
                                        <h6>Couleur du fond :</h6>
                                        <input type="color" class="form-control" name="prefixColor" required>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                            </button>
                            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">
                                <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                            </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($prefixesModel->getPrefixes() as $prefix) : ?>
                        <tr>
                            <td><span class="px-2 rounded-2"
                                      style="color: <?= $prefix->getTextColor() ?>; background: <?= $prefix->getColor() ?>"><?= $prefix->getName() ?></span>
                            </td>
                            <td><?= $prefix->getDescription() ?></td>
                            <td>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#edit-prefix-<?= $prefix->getId() ?>">
                                    <i class="text-primary fas fa-edit me-2"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#delete-prefix-<?= $prefix->getId() ?>">
                                    <i class="text-danger fas fa-trash-alt me-2"></i>
                                </a>
                            </td>
                        </tr>
                        <!--
                        ----MODAL EDITION----
                        -->
                        <div class="modal fade text-left" id="edit-prefix-<?= $prefix->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Édition
                                            de <?= $prefix->getName() ?></h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="settings/editprefix" method="post">
                                            <?php (new SecurityManager())->insertHiddenToken() ?>
                                            <input name="prefixId" hidden value="<?= $prefix->getId() ?>">
                                            <div class="row">
                                                <div class="col-12 col-lg-6 mt-2">
                                                    <h6>Nom :</h6>
                                                    <input type="text" class="form-control" name="prefixName"
                                                           value="<?= $prefix->getName() ?>" placeholder="Annonce"
                                                           required>
                                                </div>
                                                <div class="col-12 col-lg-6 mt-2">
                                                    <h6>Description :</h6>
                                                    <input type="text" class="form-control" name="prefixDescription" value="<?= $prefix->getDescription() ?>" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-lg-6 mt-2">
                                                    <h6>Couleur du texte :</h6>
                                                    <input type="color" class="form-control" name="prefixTextColor" value="<?= $prefix->getTextColor() ?>" required>
                                                </div>

                                                <div class="col-12 col-lg-6 mt-2">
                                                    <h6>Couleur du fond :</h6>
                                                    <input type="color" class="form-control" name="prefixColor" value="<?= $prefix->getColor() ?>" required>
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
                        <div class="modal fade text-left" id="delete-prefix-<?= $prefix->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title white" id="myModalLabel160">Supression
                                            de <?= $prefix->getName() ?></h5>
                                    </div>
                                    <div class="modal-body">
                                        Supprimer ce préfixe l'enlèvera également de tout les topics auquel il est lié.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="settings/deleteprefix/<?= $prefix->getId() ?>"
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