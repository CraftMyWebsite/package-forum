<?php


namespace CMW\Controller\Forums;

use CMW\Controller\coreController;
use CMW\Model\Forums\forumsModel;
use CMW\Utils\Utils;

/**
 * Class: @ForumsController
 * @package Forums
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class forumsController extends coreController
{

    private forumsModel $forumsModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->forumsModel = new forumsModel();
    }

    public function adminAddCategoryView(): void
    {

        $forum = new forumsModel();
        view('forums', 'categories/addCategory.admin', ["forum" => $forum], 'admin');
    }

    public function adminListCategoryView(): void
    {

        $forum = new forumsModel();
        view('forums', 'categories/listCategory.admin', ["forum" => $forum], 'admin');
    }

    public function adminAddCategoryPost(): void
    {

        if (Utils::isValuesEmpty($_POST, "name", "description")) {
            echo -1;
            return;
        }

        $name = filter_input(INPUT_POST, "name");
        $description = filter_input(INPUT_POST, "description");

        $res = $this->forumsModel->createCategory($name, $description);

        echo is_null($res) ? -2 : $res->getId();
    }

    public function adminDeleteCategoryPost(int $id): void
    {

        $category = $this->forumsModel->getCategoryById($id);

        if (is_null($category)) {
            return;
        }

        $res = $this->forumsModel->deleteCategory($category);

        echo $res ? 1 : -1;

        header("location: ../list/");
        die();
    }

}