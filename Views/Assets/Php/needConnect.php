<?php
use CMW\Controller\Users\UsersController;
use CMW\Model\Forum\ForumSettingsModel;

$connectText = ForumSettingsModel::getInstance()->getOptionValue('needConnectText');
$connectTextEncoded = str_replace('"', "'", $connectText);
?>

<script>
    var isLoggedIn = <?= UsersController::isUserLogged() ? 'true' : 'false'; ?>;
    var elementsToToggle = document.querySelectorAll('.needConnect');
    for (var i = 0; i < elementsToToggle.length; i++) {
        if (!isLoggedIn) {
            elementsToToggle[i].innerHTML = "<?= $connectTextEncoded ?>";
        }
    }
</script>