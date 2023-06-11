<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "Paramètres";
$description = "desc";

/* @var CMW\Controller\Forum\SettingsController $iconNotRead */
/* @var CMW\Controller\Forum\SettingsController $iconImportant */
/* @var CMW\Controller\Forum\SettingsController $iconPin */
/* @var CMW\Controller\Forum\SettingsController $iconClosed */
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-sliders"></i> <span class="m-lg-auto">Paramètres</span></h3>
</div>


<section class="row">
    <div class="col-12 col-lg-6">
        <form action="settings/applyicons" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="card">
                <div class="card-header">
                    <h4>Icônes :</h4>
                </div>
                <div class="card-body row">
                    <div class="col-12 col-lg-6 mb-4">
                        <div class="card-in-card me-2 p-3">
                            <h6>Non lue :</h6>
                            <div class="text-center mb-2">
                                <i style="font-size : 3rem;" class="<?= $iconNotRead ?>"></i>
                            </div>
                            <input type="text" class="form-control" name="icon_notRead" value="<?= $iconNotRead ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-4">
                        <div class="card-in-card me-2 p-3">
                            <h6>Important :</h6>
                            <div class="text-center mb-2">
                                <i style="font-size : 3rem;" class="<?= $iconImportant ?>"></i>
                            </div>
                            <input type="text" class="form-control" name="icon_important" value="<?= $iconImportant ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-4">
                        <div class="card-in-card me-2 p-3">
                            <h6>Épingler :</h6>
                            <div class="text-center mb-2">
                                <i style="font-size : 3rem;" class="<?= $iconPin ?>"></i>
                            </div>
                            <input type="text" class="form-control" name="icon_pin" value="<?= $iconPin ?>" required>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-4">
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
            <div class="card-body">

            </div>
        </div>
    </div>
</section>