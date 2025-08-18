<?php

namespace COQPIT\Plugins\Core\WordPress;

class Environment {

    private $options;

    public function init()
    {
        $this->options = ['Local', 'Préproduction', 'Production'];

        add_action('init',              [$this, 'defineEnvironment']);
        add_action('admin_init',        [$this, 'environmentOptions']);
        add_action('admin_bar_menu',    [$this, 'displayEnvironment'], 500);
        add_action('wp_head',           [$this, 'adminBarStyle']);
    }

    public function defineEnvironment()
    {
        $value = (get_option('coqpit_environment')) ?: __('Local', 'coqpit-core');
        define('COQPIT_ENVIRONMENT', $value);
    }

    public function environmentOptions()
    {
        add_settings_section( 'coqpit-environment-settings', __( 'Environnement', 'coqpit-core' ), '__return_false', 'general' );

        register_setting('general', 'coqpit_environment', [
            'type'      => 'string',
            'default'   => __('Local', 'coqpit-core'),
        ]);

        add_settings_field('coqpit-environment-field', __('Type d\'environnement', 'coqpit-core'), [$this, 'renderEnvironmentSetting'], 'general', 'coqpit-environment-settings', [
            'label_for' => 'coqpit_environment'
        ]);
    }

    public function renderEnvironmentSetting ()
    {
        $value = (get_option('coqpit_environment')) ?: __('Local', 'coqpit-core');
        echo '<select id="coqpit-environment-field" name="coqpit_environment"/>';
        foreach ($this->options as $label){
            echo '<option value="'.$label.'" '.selected($value, $label).'>'.$label.'</option>';
        }
        echo '</select>';
        echo '<p style="max-width: 400px">'.__('Permet de définir l\'environnement sur lequel se trouve le site installé', 'coqpit-core').'</p>';
    }

    public function displayEnvironment($adminBar)
    {
        $value = (get_option('coqpit_environment')) ?: __('Local', 'coqpit-core');
        $class = 'local';

        if($value == __('Production', 'coqpit-core')){
            $class = 'production';
        }

        if($value == __('Préproduction', 'coqpit-core')){
            $class = 'preproduction';
        }

        $adminBar->add_menu([
            'id'        => 'coqpit-environment',
            'parent'    => null,
            'href'      => null,
            'meta'      => ['class' => $class],
            'title'     => $value,
        ]);
    }

    public function adminBarStyle()
    {
        if(is_admin_bar_showing()){
            echo '<style type="text/css">';
            echo '
            #wpadminbar ul li#wp-admin-bar-coqpit-environment{
                float:right
            }
            
            #wpadminbar ul li#wp-admin-bar-coqpit-environment>div{
                font-weight:500;
                line-height:2.4
            }
            #wpadminbar ul li#wp-admin-bar-coqpit-environment.preproduction>div{
                background-color:#fcd426;
                color:#000
            }
            
            #wpadminbar ul li#wp-admin-bar-coqpit-environment.production>div{
                background-color:#67ab2a;
                color:#fff
            }
            
            #wpadminbar ul li#wp-admin-bar-coqpit-environment.local>div{
                background-color:#D42C24;
                color:#fff
            }';
            echo '</style>';
        }
    }

}
