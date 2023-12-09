<?php

?>

<script>
    let hash = window.location.hash;
    if (hash) {
        hash = hash.substring(1);

// Fonction pour afficher l'élément avec un décalage de 200 pixels vers le haut
        function scrollToElementWithOffset() {
            var element = document.getElementById(hash);
            if (element) {
                var offsetTop = element.offsetTop - 200;
                window.scrollTo(0, offsetTop);
            }
        }

// Appelez la fonction scrollToElementWithOffset lorsque la page a fini de se charger
        window.addEventListener('load', scrollToElementWithOffset);

// Fonction pour faire clignoter la bordure toutes 150 ms
        function toggleHighlight() {
            var element = document.getElementById(hash);
            if (element) {
                if (element.style.border === "2px solid blue") {
                    element.style.border = "1px solid #E5E7EB";
                } else {
                    element.style.border = "2px solid blue";
                }
            }
        }

// Appelez la fonction toggleHighlight toutes les 150 ms
        var interval = setInterval(toggleHighlight, 150);

// Arrêtez le clignotement après un certain temps
        setTimeout(function () {
            clearInterval(interval);
            var element = document.getElementById(hash);
            if (element) {
                element.style.border = "1px solid #E5E7EB";
            }
        }, 1200);
    }
</script>