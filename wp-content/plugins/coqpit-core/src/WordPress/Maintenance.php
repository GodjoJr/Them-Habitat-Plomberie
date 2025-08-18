<?php

namespace COQPIT\Plugins\Core\WordPress;

class Maintenance {

    public function init()
    {

        add_action('admin_menu',            [$this, 'optionPage']);
        add_action('customize_register',    [$this, 'addCustomizerSection']);

        $maintenanceEnabled = (get_theme_mod( 'coqpit_maintenance_enabled' )) ?: false;

        if($maintenanceEnabled){
            add_action('admin_bar_menu',    [$this, 'displayMaintenance'], 500);
            add_action('wp_head',           [$this, 'adminBarStyle']);
            add_action('template_redirect', [$this, 'redirectToHomepage']);

            add_filter('template_include',  [$this, 'loadMaintenanceTemplate']);
            add_filter('body_class',        [$this, 'addMaintenanceBodyClass']);
            add_filter('wp_title',          [$this, 'forceMaintenancePageTitle']);
            add_filter('wpseo_title',       [$this, 'forceMaintenancePageTitle']);
        }
    }

    public function optionPage()
    {
        add_options_page(
            __('Maintenance', 'coqpit-core'),
            __('Maintenance', 'coqpit-core'),
            'manage_options',
            'maintenance',
            [$this, 'redirectToCustomizer']
        );
    }

    public function redirectToCustomizer()
    {
        wp_safe_redirect(wp_customize_url());
    }

    public function addCustomizerSection($wpCustomizer)
    {
        $wpCustomizer->add_setting('coqpit_maintenance_enabled',        ['default' => false]);
        $wpCustomizer->add_setting('coqpit_maintenance_title',          ['default' => __('Site en maintenance', 'coqpit-core')]);
        $wpCustomizer->add_setting('coqpit_maintenance_description',    ['default' => __('Nous effectuons actuellement une maintenance sur le site, nous revenons bientôt !', 'coqpit-core')]);
        $wpCustomizer->add_setting('coqpit_maintenance_picture',        ['default' => '']);

        $wpCustomizer->add_section('coqpit-maintenance', [
            'title'         => __('Maintenance', 'coqpit-core'),
            'priority'      => 30,
            'description'   => __('Gestion des informations affichées en avec le mode maintenance activé', 'coqpit-core'),
        ]);

        $wpCustomizer->add_control(
            new \WP_Customize_Control($wpCustomizer, 'coqpit_maintenance_enabled', [
                'label'         => __( 'Activer/Désactiver', 'coqpit-core' ),
                'type'          => 'checkbox',
                'description'   => sprintf(__('Active ou désactive la maintenance sur tout le site, attention de définir le template %s dans le thème. Un fichier d\'exemple est disponible dans le plugin COQPIT Core et sera utilisé par défaut.', 'coqpit-core'), '<span style="background-color: rgba(200,200,200);font-family: monospace; unicode-bidi: isolate; color: black; margin: 0; box-sizing: border-box; padding: 1px 5px 1px 5px; border-radius: 3px; vertical-align: middle; font-size: 12px; display: inline-block;">{theme}/templates/maintenance.php</span>'),
                'section'       => 'coqpit-maintenance',
                'settings'      => 'coqpit_maintenance_enabled'
            ])
        );

        $wpCustomizer->add_control(
            new \WP_Customize_Control($wpCustomizer, 'coqpit_maintenance_title', [
                'label'     => __( 'Titre de la page', 'coqpit-core' ),
                'section'   => 'coqpit-maintenance',
                'settings'  => 'coqpit_maintenance_title'
            ])
        );

        $wpCustomizer->add_control(
            new \WP_Customize_Control($wpCustomizer, 'coqpit_maintenance_description', [
                'label'     => __( 'Description', 'coqpit-core' ),
                'type'      => 'textarea',
                'section'   => 'coqpit-maintenance',
                'settings'  => 'coqpit_maintenance_description'
            ])
        );

        $wpCustomizer->add_control(
            new \WP_Customize_Image_Control($wpCustomizer, 'coqpit_maintenance_picture', [
                'label'     => __( 'Image', 'coqpit-core' ),
                'type'      => 'image',
                'section'   => 'coqpit-maintenance',
                'settings'  => 'coqpit_maintenance_picture'
            ])
        );
    }

    public function displayMaintenance($adminBar)
    {
        $adminBar->add_menu([
            'id'        => 'coqpit-maintenance',
            'parent'    => null,
            'href'      => wp_customize_url(),
            'title'     => __('Maintenance activée', 'coqpit-core'),
        ]);
    }

    public function adminBarStyle()
    {
        if(is_admin_bar_showing()){
            echo '<style>';
            echo '
            #wpadminbar ul li#wp-admin-bar-coqpit-maintenance > div,
            #wpadminbar ul li#wp-admin-bar-coqpit-maintenance > a{
                line-height: 2.4;
                font-weight: 500;
                background-color: #D42C24;
                color: white;
            }';
            echo '</style>';
        }
    }

    public function loadMaintenanceTemplate($template)
    {
        if(is_front_page() && !is_user_logged_in()){
            $newTemplate = get_template_directory().'/templates/maintenance.php';
            if(file_exists($newTemplate)){
                return $newTemplate;
            }

            $newTemplate = get_template_directory().'/templates/page/maintenance.php';
            if(file_exists($newTemplate)){
                return $newTemplate;
            }

            $newTemplate = COQPIT_CORE_PATH.'templates/maintenance.php';
            if(file_exists($newTemplate)){
                return $newTemplate;
            }

        }

        return $template;
    }

    public function redirectToHomepage()
    {
        if(!is_front_page() && !is_user_logged_in()){
            wp_redirect(home_url());
        }
    }

    public function addMaintenanceBodyClass($classes)
    {
        if(is_front_page() && !is_user_logged_in()) {
            $classes[] = 'has-maintenance';
        }
        return $classes;
    }

    public function forceMaintenancePageTitle($title)
    {
        if(is_front_page() && !is_user_logged_in()){
            return (get_theme_mod('coqpit_maintenance_title')) ?: __('Site en maintenance', 'coqpit-core');
        }

        return $title;
    }

}
