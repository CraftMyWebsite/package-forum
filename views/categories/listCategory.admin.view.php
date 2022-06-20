<?php

$title = FORUMS_CATEGORY_LIST_TITLE;
$description = FORUMS_CATEGORY_LIST_DESC;


/** @var \CMW\Entity\Forums\forumsModel $forum */


$search = CORE_DATATABLES_LIST_SEARCH;
$info = CORE_DATATABLES_LIST_INFO__VANILLA;
$perPage = CORE_DATATABLES_LIST_SETLIMIT;
$empty = CORE_DATATABLES_LIST_EMPTYTABLE;

$id = FORUMS_ID;
$name = FORUMS_NAME;
$description = FORUMS_DESC;
$action = FORUMS_ACTION;

$styles = '<link rel="stylesheet" href="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/simple-datatables/css/simple-datatables.css" />';
$scripts = '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/simple-datatables/js/simple-datatables.js"></script>';

$scripts .= <<<HTML
    
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

<?php ob_start(); ?>
    <!-- main-content -->

    <!-- main-content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Contenu ici -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= FORUMS_CATEGORY_LIST_CARD_TITLE ?></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="myTable" class="table table-bordered table-striped">
                                <tbody>
                                <?php foreach ($forum->getCategories() as $category) : ?>
                                    <tr>
                                        <td><?= $category->getId() ?></td>
                                        <td><?= $category->getName() ?></td>
                                        <td><?= $category->getDescription() ?></td>
                                        <td><a href="<?= $category->getAdminDeleteLink() ?>">Supprimer</a></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-12">
                                    <div class="w-50 ml-auto text-right">
                                        <a href="./add" class="btn btn-primary">
                                            Ajouter une catégorie
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>
    <!-- /.main-content -->
<?php $content = ob_get_clean(); ?>