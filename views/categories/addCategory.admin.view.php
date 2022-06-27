<?php

$title = FORUMS_CATEGORY_ADD_TITLE;
$description = FORUMS_CATEGORY_ADD_DESC;


/** @var \CMW\Entity\Forums\forumEntity $forum */
?>

<?php ob_start(); ?>
    <!-- main-content -->

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="" method="post" id="addForumCategory">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><?= FORUMS_CATEGORY_ADD_CARD_TITLE ?> :</h3>
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pen"></i></span>
                                    </div>
                                    <input type="text" name="name" class="form-control"
                                           placeholder="<?= FORUMS_CATEGORY_NAME ?>" required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                    </div>
                                    <textarea type="text" name="description" class="form-control"
                                              placeholder="<?= FORUMS_CATEGORY_DESCRIPTION ?>" required></textarea>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit"
                                        class="btn btn-primary float-right"><?= USERS_LIST_BUTTON_SAVE ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>

    <script>

        callPostFunction("addForumCategory")

    </script>
    <!-- /.main-content -->
<?php $content = ob_get_clean(); ?>