<?php


$title = "Forum - Rôles";
$description = "desc";

/* @var CMW\Model\Forum\ForumSettingsModel $visitorCanViewForum */

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gavel"></i> <span class="m-lg-auto">Rôles</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-4">
        <div class="card ">
            <div class="card-header">
                <h6>Paramètres</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="support_settings_status_defined_by_customer" name="visitorCanViewForum" <?= $visitorCanViewForum ? 'checked' : '' ?>>
                        <label class="form-check-label" for="support_settings_status_defined_by_customer">Accès en lecture pour les vitisteurs
                            <i data-bs-toggle="tooltip" title="Si cette option est active la permission de consulter le forum pour les rôle n'est plus active, cette option est prioritaire" class="fa-sharp fa-solid fa-circle-question"></i></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>