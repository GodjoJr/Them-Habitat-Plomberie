<?php

namespace COQPIT\Plugins\Core\RGPD;

use COQPIT\Plugins\Core\Template\Scripts;

class TarteAuCitron {

    public static function init() :void
    {
        add_action( 'wp_enqueue_scripts', [self::class, 'enqueueScript']);
        add_action('admin_init',        [self::class, 'GPDROptions']);
    }

    public static function enqueueScript() :void
    {
        $enabled = get_option( 'coqpit_tarte_au_citron' );
        if($enabled){
            $url = parse_url(get_site_url());
            wp_enqueue_style( 'tarte-au-citron-css', COQPIT_CORE_URI.'/assets/css/tarte-au-citron.css' );
            wp_enqueue_script( 'tarte-au-citron', 'https://tarteaucitron.io/load.js?domain='.$url['host'].'&uuid=7605c30f877764834ce93a1424319675a1abaf1b', [], '1.0.0', true );
        }
    }

    public static function GPDROptions()
    {
        add_settings_section( 'coqpit-gpdr-settings', __( 'Paramètres RGPD', 'coqpit-core' ), '__return_false', 'general' );

        register_setting('general', 'coqpit_tarte_au_citron', [
            'type' => 'boolean',
            'default' => false,
        ]);

        add_settings_field('coqpit-tarte-au-citron-field', __('Activer l\'utilisation de Tarte au citron', 'coqpit-core'), [self::class, 'renderGPDRSetting'], 'general', 'coqpit-gpdr-settings', [
            'label_for' => 'coqpit_tarte_au_citron'
        ]);
    }

    public static function renderGPDRSetting ()
    {
        $enabled = get_option( 'coqpit_tarte_au_citron' );
        $checked = ($enabled) ? ' checked="checked"' : '';
        echo '<label>';
        echo '<input id="coqpit-tarte-au-citron-field" name="coqpit_tarte_au_citron" type="checkbox"'.$checked.'/>';
        echo '<p style="max-width: 400px">'.__('Active ou désactive l\'utilisation de tarte au citron', 'coqpit-core').'</p>';
        echo '</label>';
    }

}
