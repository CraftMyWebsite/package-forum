<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("forum.forum.list.title");
$description = LangManager::translate("forum.forum.list.description");
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span class="m-lg-auto">Gestion</span></h3>
</div>

<section class="row">
	<div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Catégories et forums</h4>
            </div>
            <div class="card-body">
                <?php if ($categoryModel->getCategories()): ?>
                <?php foreach ($categoryModel->getCategories() as $category):?>
                    <div class="card-in-card table-responsive mb-4">
                        <table class="table-borderless table table-hover mt-1">
                            <thead>
                                <tr>
                                    <th id="categorie-<?= $category->getId() ?>"> <?= $category->getFontAwesomeIcon() ?> <?= $category->getName() ?> -<i> <small><?= mb_strimwidth($category->getDescription(), 0, 45, '...') ?></small></i></th>
                                    <th class="text-end">
                                        <a type="button" data-bs-toggle="modal" data-bs-target="#add-forum-<?= $category->getId() ?>">
                                            <i class="text-success me-3 fa-solid fa-circle-plus"></i>
                                        </a>
                                        <a type="button" data-bs-toggle="modal" data-bs-target="#edit-categories-<?= $category->getId() ?>">
                                            <i class="text-primary me-3 fas fa-edit"></i>
                                        </a>
                                        <a type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $category->getId() ?>">
                                            <i class="text-danger fas fa-trash-alt"></i>
                                        </a>
                                    </th>
                                </tr>

                                <!--
                                	--MODAL AJOUT FORUM--
                                -->
                                <div class="modal fade " id="add-forum-<?= $category->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
								    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
								        <div class="modal-content">
								            <div class="modal-header bg-primary">
								                <h5 class="modal-title white" id="myModalLabel160">Ajout d'un forum dans <?= $category->getName() ?></h5>
								            </div>
								            <div class="modal-body">
								                <form method="post" action="forums/add">
								                    <?php (new SecurityManager())->insertHiddenToken() ?>       
													<input hidden type="text" name="category_id" value="<?= $category->getId() ?>" required>
														<h6>Icon :</h6>
								                        <div class="form-group position-relative has-icon-left">
								                            <input type="text" class="form-control" name="icon" required placeholder="fas fa-users">
								                            <div class="form-control-icon">
								                                <i class="fas fa-icons"></i>
								                            </div>
								                            <small class="form-text">Retrouvez la liste des icones sur le site de <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">FontAwesome.com</a></small>
								                        </div>  
								                        <h6>Nom :</h6>
								                        <div class="form-group position-relative has-icon-left">
								                            <input type="text" class="form-control" name="name" required placeholder="Général">
								                            <div class="form-control-icon">
								                                <i class="fas fa-heading"></i>
								                            </div>
								                        </div>
								                        <h6>Déscription :</h6>
								                        <div class="form-group position-relative has-icon-left">
								                            <input type="text" class="form-control" name="description" required placeholder="Parlez de tout et de rien">
								                            <div class="form-control-icon">
								                                <i class="fas fa-paragraph"></i>
								                            </div>
								                        </div>         
								            </div>
								            <div class="modal-footer">
								                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
								                    <i class="bx bx-x"></i>
								                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
								                </button>
								                <button type="submit" class="btn btn-primary ml-1">
								                    <i class="bx bx-check"></i>
								                    <span class=""><?= LangManager::translate("core.btn.add") ?></span>
								                </button>    
								                </form>                            
								            </div>
								        </div>
								    </div>
								</div>

                                <!--
                                    --MODAL EDITION CATEGORIE--
                                -->
                                <div class="modal fade " id="edit-categories-<?= $category->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title white" id="myModalLabel160">Édition de <?= $category->getName() ?></h5>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" action="categories/edit/<?= $category->getId() ?>">
                                                    <?php (new SecurityManager())->insertHiddenToken() ?>       
                                                        <h6>Icon :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="icon" required placeholder="fas fa-users" value="<?= $category->getIcon() ?>">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-icons"></i>
                                                            </div>
                                                            <small class="form-text">Retrouvez la liste des icones sur le site de <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">FontAwesome.com</a></small>
                                                        </div>  
                                                        <h6>Nom :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="name" required placeholder="Général" value="<?= $category->getName() ?>">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-heading"></i>
                                                            </div>
                                                        </div>
                                                        <h6>Déscription :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="description" required placeholder="Parlez de tout et de rien" value="<?= $category->getDescription() ?>">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-paragraph"></i>
                                                            </div>
                                                        </div>         
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                    <i class="bx bx-x"></i>
                                                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                </button>
                                                <button type="submit" class="btn btn-primary ml-1">
                                                    <i class="bx bx-check"></i>
                                                    <span class=""><?= LangManager::translate("core.btn.edit") ?></span>
                                                </button>    
                                                </form>                            
                                            </div>
                                        </div>
                                    </div>
                                </div>

								<!--
                                	--MODAL SUPPRESSION CATEGORIE--
                                -->
                                <div class="modal fade text-left" id="delete-<?= $category->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title white" id="myModalLabel160">Supression de : <?= $category->getName() ?></h5>
                                            </div>
                                            <div class="modal-body">
                                                Cette supression est définitive
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                    <i class="bx bx-x"></i>
                                                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                </button>
                                                <a href="categories/delete/<?= $category->getId() ?>" class="btn btn-danger ml-1">
                                                    <i class="bx bx-check"></i>
                                                    <span class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                                </a>                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </thead>
                            <tbody>
                                <?php foreach ($forumModel->getForumByParent($category->getId()) as $forumObj): ?>
                                <tr id="forum-<?= $forumObj->getId() ?>">
                                    <td class="ps-4 text-bold-500"><?= $forumObj->getFontAwesomeIcon() ?> <?= $forumObj->getName() ?> - <i><small><?= mb_strimwidth($forumObj->getDescription(), 0, 45, '...') ?></small></i>
                                    </td>
                                    <td class="text-end">
                                        <a target="_blank" href="<?= Utils::getHttpProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . $forumObj->getLink()?>"><i class="me-3 fa-solid fa-up-right-from-square"></i></a>
                                        <a type="button" data-bs-toggle="modal" data-bs-target="#edit-forums-<?= $forumObj->getId() ?>">
                                            <i class="text-primary me-3 fas fa-edit"></i>
                                        </a>
                                        <a type="button" data-bs-toggle="modal" data-bs-target="#deletee-<?= $forumObj->getId() ?>">
                                            <i class="text-danger fas fa-trash-alt"></i>
                                        </a>
                                    </td> 
                                </tr>

                                <!--
                                    --MODAL EDITION FORUM--
                                -->
                                <div class="modal fade " id="edit-forums-<?= $forumObj->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title white" id="myModalLabel160">Édition de <?= $forumObj->getName() ?></h5>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" action="forums/edit/<?= $forumObj->getId() ?>">
                                                    <?php (new SecurityManager())->insertHiddenToken() ?>   
                                                    <h6>Changer de catégorie :</h6>
                                                    <div class="form-group">
                                                        <select class="choices form-select" name="categoryId" required>
                                                            <?php foreach ($categoryModel->getCategories() as $category): ?>
                                                                <option value="<?= $category->getId() ?>">
                                                                    <?= $category->getName() ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>    
                                                        <h6>Icon :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="icon" required placeholder="fas fa-users" value="<?= $forumObj->getIcon() ?>">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-icons"></i>
                                                            </div>
                                                            <small class="form-text">Retrouvez la liste des icones sur le site de <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">FontAwesome.com</a></small>
                                                        </div>  
                                                        <h6>Nom :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="name" required placeholder="Général" value="<?= $forumObj->getName() ?>">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-heading"></i>
                                                            </div>
                                                        </div>
                                                        <h6>Déscription :</h6>
                                                        <div class="form-group position-relative has-icon-left">
                                                            <input type="text" class="form-control" name="description" required placeholder="Parlez de tout et de rien" value="<?= $forumObj->getDescription() ?>">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-paragraph"></i>
                                                            </div>
                                                        </div>         
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                    <i class="bx bx-x"></i>
                                                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                </button>
                                                <button type="submit" class="btn btn-primary ml-1">
                                                    <i class="bx bx-check"></i>
                                                    <span class=""><?= LangManager::translate("core.btn.edit") ?></span>
                                                </button>    
                                                </form>                            
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--
                                    --MODAL SUPRESSION FORUM--
                                -->
                                <div class="modal fade text-left" id="deletee-<?= $forumObj->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title white" id="myModalLabel160">Supression de : <?= $forumObj->getName() ?></h5>
                                            </div>
                                            <div class="modal-body">
                                                Cette supression est définitive
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                    <i class="bx bx-x"></i>
                                                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                </button>
                                                <a href="forums/delete/<?= $forumObj->getId() ?>" class="btn btn-danger ml-1">
                                                    <i class="bx bx-check"></i>
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
                <?php endforeach; ?>
                <?php else: ?>
                <div class="alert alert-info">Merci de créer une catégorie pour commencer à utiliser le Forum</div>
            <?php endif ?>
                <div class="divider">
                    <a type="button" data-bs-toggle="modal" data-bs-target="#add-cat">
                        <div class="divider-text"><i class="fa-solid fa-circle-plus"></i> Ajouter une catégorie</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<!--
    --MODAL AJOUT CATEGORIE--
-->
<div class="modal fade " id="add-cat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title white" id="myModalLabel160"><?= LangManager::translate("wiki.title.add_category") ?></h5>
            </div>
            <div class="modal-body">
                <form method="post" action="categories/add">
                    <?php (new SecurityManager())->insertHiddenToken() ?>    
                    	<h6>Icon :</h6>
                        <div class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control" name="icon" required placeholder="fas fa-users">
                            <div class="form-control-icon">
                                <i class="fas fa-icons"></i>
                            </div>
                            <small class="form-text">Retrouvez la liste des icones sur le site de <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">FontAwesome.com</a></small>
                        </div>         
                        <h6>Nom :</h6>
                        <div class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control" name="name" required placeholder="Communauté">
                            <div class="form-control-icon">
                                <i class="fas fa-heading"></i>
                            </div>
                        </div>
                        <h6>Description :</h6>
                        <div class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control" name="description" required placeholder="L'éspace communautaire">
                            <div class="form-control-icon">
                                <i class="fas fa-paragraph"></i>
                            </div>
                        </div>          
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x"></i>
                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                </button>
                <button type="submit" class="btn btn-primary ml-1">
                    <i class="bx bx-check"></i>
                    <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                </button>    
                </form>                            
            </div>
        </div>
    </div>
</div>


