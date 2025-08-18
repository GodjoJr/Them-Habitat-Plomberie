<?php

if(apply_filters('coqpit_core_loaded', false)){

    \COQPIT\Plugins\Core\Template\Vite::register(__DIR__.'/resources/js/app.js', __DIR__.'/assets/manifest.json');

    COQPIT\Plugins\Core\Template\Menus::register([
        'legal_navigation' => __('Navigation légale', 'socab')
    ]);
}