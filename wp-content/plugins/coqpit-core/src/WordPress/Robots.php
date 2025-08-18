<?php

namespace COQPIT\Plugins\Core\WordPress;

class Robots {

    /**
     * Check if website running on preproduction and add no index only for that
     * @return void
     */
    public static function check() {

        if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] != 'localhost') {
            $domain = array_reverse(explode('.', $_SERVER['HTTP_HOST']))[1];
            if($domain == 'mycoqpit'){
                add_action('wp_head', function (){
                    echo '<meta name="robots" content="noindex">';
                });
            }
        }

    }

}
