document.addEventListener("DOMContentLoaded", function () {
    // Recherchez la classe "theme-dark" ou "dark" sur la balise <body> ou un autre élément pertinent.
    const bodyElement = document.body;
    const hasDarkTheme = bodyElement.classList.contains("theme-dark") || bodyElement.classList.contains("dark");

    // Définissez le skin en fonction de hasDarkTheme
    const tinymceSkin = hasDarkTheme ? 'theme-dark' : 'theme-light';

    const images_upload_handler = (blobInfo) => new Promise((success, failure) => {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();

        xhr.open('POST', '/editor/upload/image');
        const imgElement = tinymce.activeEditor.selection.getNode();

        xhr.onload = function() {
            if (xhr.status === 200) {
                let json = JSON.parse(xhr.responseText);

                if (json && typeof json.location === 'string') {
                    success(json.location);
                } else {
                    failure('Réponse JSON invalide');
                    tinymce.activeEditor.dom.remove(imgElement);
                }
            } else {
                failure('Erreur lors de l\'upload : ' + xhr.status);
                tinymce.activeEditor.dom.remove(imgElement);
            }
        };

        xhr.onerror = function() {
            failure('Erreur réseau ou problème d\'accès au serveur');
            tinymce.activeEditor.dom.remove(imgElement);
        };

        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    });


    tinymce.init({
        selector: '.tinymce',
        skin: tinymceSkin,
        content_css: tinymceSkin,
        promotion: false,
        toolbar_sticky: true,
        toolbar_mode: 'sliding',
        plugins: ['emoticons', 'image', 'autoresize', 'wordcount', 'advlist', 'lists', 'charmap', 'codesample', 'code', 'directionality', 'fullscreen', 'link', 'insertdatetime', 'media', 'pagebreak', 'nonbreaking', 'preview', 'quickbars', 'searchreplace', 'table', 'visualblocks', 'visualchars'],
        toolbar:
            'undo redo | ' +
            'formatpainter casechange blocks fontsizeselect | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bold italic strikethrough | ' +
            'forecolor backcolor removeformat |' +
            'bullist numlist outdent indent | ' +
            'table | ' +
            'visualchars visualblocks ltr rtl | ' +
            'searchreplace nonbreaking pagebreak|' +
            'link media image insertdatetime |' +
            'emoticons charmap |' +
            'wordcount codesample code |' +
            'preview fullscreen help',
        menubar: false,
        min_height: 350,
        images_file_types: 'jpg,svg,webp',
        file_picker_types: 'file image media',
        statusbar: false,
        extended_valid_elements: 'a[class=needConnect|href|title|target|rel]',
        relative_urls: false,
        images_upload_handler:images_upload_handler,
    });
});