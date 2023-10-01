<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Forum\ForumCategoryEntity $category */
/* @var \CMW\Entity\Forum\ForumPermissionRoleEntity[] $ForumRoles */

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span
            class="m-lg-auto">Ajout d'un forum dans <?= $category->getName() ?></span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6 mx-auto">
        <form class="card" method="post">
            <div class="card-body">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <h6>Icon :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="icon" required
                           placeholder="fas fa-users">
                    <div class="form-control-icon">
                        <i class="fas fa-icons"></i>
                    </div>
                    <small class="form-text">Retrouvez la liste des icones sur le
                        site de <a href="https://fontawesome.com/search?o=r&m=free"
                                   target="_blank">FontAwesome.com</a></small>
                </div>
                <h6>Nom :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="name" required
                           placeholder="Général">
                    <div class="form-control-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                </div>
                <h6>Déscription :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="description"
                           required placeholder="Parlez de tout et de rien">
                    <div class="form-control-icon">
                        <i class="fas fa-paragraph"></i>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-check"></i>
                    <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                </button>
            </div>
        </form>
    </div>
</section>
