<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

Website::setTitle('Forum');
Website::setDescription('Report');

?>
<form action="" method="post">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <label for="reportTopic">Reason</label>
    <select id="reportTopic" name="reason">
        <option value="1">Inappropriate response</option>
        <option value="2">Shocking content</option>
        <option value="3">Harassment, discrimination...</option>
        <option value="0">Other</option>
    </select>
    <button type="submit">Send</button>
</form>