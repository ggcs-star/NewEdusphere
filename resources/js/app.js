import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

import tinymce from 'tinymce/tinymce';
import 'tinymce/models/dom/model';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/skins/ui/oxide/content.css';
import 'tinymce/skins/content/default/content.css';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/media';
import 'tinymce/plugins/table';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/help';
import 'tinymce/plugins/wordcount';

if (document.querySelector('.tinymce-editor')) {
    tinymce.init({
        selector: '.tinymce-editor',
        license_key: 'gpl',
        skin: false,
        content_css: false,
        height: 320,
        menubar: false,
        plugins: 'lists link image media table code fullscreen help wordcount',
        toolbar: 'bold underline forecolor | bullist numlist | alignleft aligncenter alignright | table link image media | fullscreen code | help',
        branding: false,
    });
}
