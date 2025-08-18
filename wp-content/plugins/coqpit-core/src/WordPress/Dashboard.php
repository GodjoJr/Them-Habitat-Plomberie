<?php

namespace COQPIT\Plugins\Core\WordPress;

class Dashboard {

    private array $allowedMetaBoxes = [
        'dashboard_right_now',
        'coqpit_dashboard_metabox'
    ];

    public function init()
    {
        add_action('wp_dashboard_setup', [$this, 'addCustomMetaBox']);
        add_action('wp_dashboard_setup', [$this, 'clearMetaBoxes'], 999);
    }

    public static function allowMetaBox($metaBoxID)
    {
        add_filter('coqpit_allowed_dashboard_metaboxes', function ($metaBoxes) use ($metaBoxID){
            $metaBoxes[] = $metaBoxID;
            return $metaBoxes;
        }, 10, 1);
    }

    /**
     * Register custom MetaBox for customers
     * @return void
     */
    public function addCustomMetaBox()
    {
        add_meta_box('coqpit_dashboard_metabox', 'COQPIT - Informations', [$this, 'displayMetaBox'], 'dashboard', 'normal', 'low');
    }

    /**
     * Render custom MetaBox for customers
     * @return void
     */
    public function displayMetaBox()
    {
        $default_html = '<div class="coqpit-dashboard-nothing"><p>Rien à afficher</p></div>';
        echo apply_filters('coqpit_dashboard_content', $default_html);
    }

    /**
     * Remove default WordPress MetaBoxes on Dashboard
     * @return void
     */
    public function clearMetaBoxes () :void
    {

        $this->allowedMetaBoxes = apply_filters('coqpit_allowed_dashboard_metaboxes', $this->allowedMetaBoxes);

        $contexts = $this->getMetaBoxes();

        foreach ($contexts as $context => $types){
            foreach ($types as $type){
                foreach ($type as $id => $metaBoxe){
                    if(!in_array($id, $this->allowedMetaBoxes)){
                        remove_meta_box($id, 'dashboard', $context);
                    }
                }
            }
        }

        remove_action('welcome_panel', 'wp_welcome_panel');
    }

    public function getMetaBoxes() {
        global $wp_meta_boxes;
        return $wp_meta_boxes['dashboard'];
    }

}
