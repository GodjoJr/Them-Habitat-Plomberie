<?php

namespace COQPIT\Plugins\Core\Template;

class Menus {

    public array $menus = [];

    /**
     * Register menus
     * @param array $menus
     * @return void
     */
    public static function register(array $menus = [])
    {
        $object = (new self);
        $object->menus = $menus;
        add_action( 'after_setup_theme', [$object, 'registerMenus']);
    }


    /**
     * WordPress register menus
     * @return void
     */
    public function registerMenus() :void
    {
        register_nav_menus($this->menus);
    }

}
