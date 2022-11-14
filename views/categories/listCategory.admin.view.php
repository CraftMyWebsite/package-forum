<?php

use CMW\Manager\Lang\LangManager;

/** @var \CMW\Model\Forum\ForumModel $forum */

$title = LangManager::translate("forum.category.list.title");
$description = LangManager::translate("forum.category.list.description");

$search = LangManager::translate("core.datatables.list.search");
$info = LangManager::translate("core.datatables.list.info_vanilla");
$perPage = LangManager::translate("core.datatables.list.setlimit");
$empty = LangManager::translate("core.datatables.list.emptytable");

$id = LangManager::translate("forum.id");
$name = LangManager::translate("forum.name");
$description = LangManager::translate("forum.description");
$action = LangManager::translate("forum.action");

$scripts = <<<HTML
    
        <script>
            const data = {
                "headings": [
                    "$id",
                    "$name",
                    "$description",
                    "$action"
                ]
            }
              
            const dataTable = new simpleDatatables.DataTable("#myTable", {
                data,
                labels: {
                    placeholder: "$search",
                    perPage: "$perPage",
                    noRows: "$empty",
                    info: "$info",
                },
            })
    </script>
    HTML;
?>


<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= LangManager::translate("forum.category.list.card_title") ?></h3>
                    </div>
                    <div class="card-body">
                        <table id="myTable" class="table table-bordered table-striped">
                            <tbody>
                            <?php foreach ($forum->getCategories() as $category) : ?>
                                <tr>
                                    <td><?= $category->getId() ?></td>
                                    <td><?= $category->getName() ?></td>
                                    <td><?= $category->getDescription() ?></td>
                                    <td>
                                        <a href="<?= $category->getAdminDeleteLink() ?>"><?= LangManager::translate("core.btn.delete") ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-12">
                                <div class="w-50 ml-auto text-right">
                                    <a href="./add" class="btn btn-primary">
                                        <?= LangManager::translate("forum.btn.add_category") ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>