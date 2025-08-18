<?php

namespace COQPIT\Plugins\ThpCore;

use COQPIT\Plugins\Core\Admin\Scripts as AdminScripts;
use COQPIT\Plugins\Core\Template\Scripts;
use COQPIT\Plugins\ThpCore\WordPress\Updater;
use COQPIT\Plugins\ThpCore\WordPress\CMS;

class THPCore
{

    public static function init()
    {
        // Register plugin updater
        Updater::register(THP_CORE_VERSION, THP_CORE_FILE);

        // Register Admin scripts and styles
        Scripts::register('thp-core', [
            'assets/css/thp-core.css',
            'assets/js/thp-core.js'
        ], THP_CORE_URI, THP_CORE_PATH, true);

        new CMS();

    }
}
