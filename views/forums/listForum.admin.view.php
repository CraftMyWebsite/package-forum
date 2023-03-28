<?php

use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");


/** @var \CMW\Model\Forum\forumModel $forum */


$search = LangManager::translate("core.datatables.list.search");
$info = LangManager::translate("core.datatables.list.info_vanilla");
$perPage = LangManager::translate("core.datatables.list.setlimit");
$empty = LangManager::translate("core.datatables.list.emptytable");

$id = LangManager::translate("forum.id");
$name = LangManager::translate("forum.name");
$description = LangManager::translate("forum.description");
$action = LangManager::translate("forum.action");
$parent = LangManager::translate("forum.parent");

$scripts = <<<HTML
    
        <script>
            const data = {
                "headings": [
                    "$id",
                    "$name",
                    "$description",
                    "$parent",
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
                        <h3 class="card-title"><?= LangManager::translate("forum.forum.list.card_title") ?></h3>
                    </div>
                    <div class="card-body">
                        <table id="myTable" class="table table-bordered table-striped">
                            <tbody>
                            <?php foreach ($forum->getForums() as $forum) : ?>
                                <tr>
                                    <td><?= $forum->getId() ?></td>
                                    <td><?= $forum->getName() ?></td>
                                    <td><?= $forum->getDescription() ?></td>
                                    <td><?= $forum->getCreated() ?></td>
                                    <td><?= $forum->getUpdate() ?></td>
                                    <td><?= ($forum->isParentCategory() ? "<i class='fa fa-book'></i>" : "<i class='fa fa-envelope-open-text'></i>") ?> <?= $forum->getName() ?></td>
                                    <td>
                                        <a href="<?= $forum->getAdminDeleteLink() ?>"><?= LangManager::translate("core.btn.delete") ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-12">
                                <div class="w-50 ml-auto text-right">
                                    <a href="./add" class="btn btn-primary">
                                        <?= LangManager::translate("forum.btn.add_forum") ?>
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