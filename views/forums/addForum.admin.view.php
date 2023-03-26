<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("forum.forum.add.title");
$description = LangManager::translate("forum.forum.add.description");

/* @var \CMW\Entity\Forum\CategoryEntity[] $categories */

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="post" id="addForum">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("forum.forum.add.card_title") ?> :</h3>
                        </div>

                        <div class="card-body">
                            <div class="col-md-6">
                                <h6><?= LangManager::translate("forum.categories") ?> :</h6>
                                <div class="form-group">
                                    <select class="choices form-select" name="category_id" required>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category->getId() ?>">
                                                <?= $category->getName() ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-pen"></i></span>
                                <input type="text" name="name" class="form-control"
                                       placeholder="<?= LangManager::translate("forum.forum.name") ?>" required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                <textarea type="text" name="description" class="form-control"
                                          placeholder="<?= LangManager::translate("forum.forum.description") ?>"
                                          required></textarea>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit"
                                    class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save") ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>