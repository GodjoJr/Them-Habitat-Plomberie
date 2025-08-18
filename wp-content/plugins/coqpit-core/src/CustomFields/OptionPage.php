<?php

namespace COQPIT\Plugins\Core\CustomFields;

class OptionPage {

    /**
     * Create ACF Option page
     * @return void
     */
    public static function addPage(array $args = [])
    {
        if( function_exists('acf_add_options_page') ) {
            acf_add_options_page($args);
        }
    }

    /**
     * Create ACF Sub Option page
     * @return void
     */
    public static function addSubPage(array $args = [])
    {
        if( function_exists('acf_add_options_sub_page') ) {

            acf_add_options_sub_page($args);

        }
    }

}
