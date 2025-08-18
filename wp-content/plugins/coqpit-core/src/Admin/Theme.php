<?php

namespace COQPIT\Plugins\Core\Admin;

class Theme {

    /**
     * Allow SVG Uploads on WordPress
     * @return void
     */
    public static function allowSVG()
    {
        add_filter('upload_mimes', function ($mimes){
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        });
    }

}
