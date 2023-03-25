<?php
/** @var \CMW\Entity\Forum\forumEntity $forum */


use CMW\Manager\Security\SecurityManager;

$title = "Ajouter un topic | {$forum->getName()}";
$description = "Ajoutez un topic au forum {$forum->getName()}";
?>

<section class="container px-4 px-lg-5 h-100" style="margin-top: 80px">
   <form action="" method="post">
       <?php (new SecurityManager())->insertHiddenToken() ?>

       <div class="align-items-center">
           <h1 class="text-dark font-weight-bold">Ajouter un topic au forum <?= $forum->getName() ?></h1>
           <hr class="divider" />

           <label> Nom du topic
               <input type="text" name="name" required>
           </label>

           <br>

           <label> Contenu
               <textarea name="content" required></textarea>
           </label>

           <br>

           <button type="submit">Envoyer</button>

       </div>
   </form>

</section>