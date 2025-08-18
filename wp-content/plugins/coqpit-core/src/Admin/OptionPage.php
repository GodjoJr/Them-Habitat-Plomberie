<?php

namespace COQPIT\Plugins\Core\Admin;

class OptionPage {

    private $id = 'coqpit-core-settings';
    private $slug = 'coqpit_core_options';

    public static function init()
    {
        $option = new self();
        $plugin = plugin_basename( COQPIT_CORE_PLUGIN_FILE );
        add_action('admin_menu',                    [$option, 'register']);
        add_filter('plugin_action_links_'.$plugin,  [$option, 'addSettingLink'], 20, 1);
    }

    public static function slug()
    {
        $option = new self();
        return $option->slug;
    }

    public function register()
    {
        add_submenu_page( 'options-general.php', 'COQPIT Core', 'COQPIT Core', 'manage_options', $this->slug, [$this, 'optionPageContent']);
    }

    public function optionPageContent()
    {
        echo '<div id="'.$this->id.'"></div>';
    }

    public function addSettingLink($links)
    {
        $newLinks[] = '<a href="'.get_admin_url().'options-general.php?page='.$this->slug.'">' . __( 'Réglages', 'coqpit-core') . '</a>';

        foreach ($links as $link){
            $newLinks[] = $link;
        }

        return $newLinks;
    }

}
