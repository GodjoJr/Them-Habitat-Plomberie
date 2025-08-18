<?php

if(apply_filters('coqpit_core_loaded', false)){
    COQPIT\Plugins\Core\Template\Scripts::register('template', [
        'assets/css/app.css',
        'assets/js/app.js'
    ],
        get_template_directory_uri(), // URL du répertoire où se trouvent les fichiers
        get_template_directory(), // Chemin absolu du répertoire où se trouvent les fichiers
        true, // Utilisation du Hot Module Replacement (HMR, rechargement CSS sans rechargement de la page) avec la nouvelle version de webpack.mix.js
    );
}