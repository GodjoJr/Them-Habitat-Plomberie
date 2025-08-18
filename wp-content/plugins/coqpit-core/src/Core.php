<?php

namespace COQPIT\Plugins\Core;

use COQPIT\Plugins\Core\Admin\OptionPage;
use COQPIT\Plugins\Core\Admin\Theme;
use COQPIT\Plugins\Core\Admin\Scripts;
use COQPIT\Plugins\Core\Elementor\Widgets;
use COQPIT\Plugins\Core\RGPD\TarteAuCitron;
use COQPIT\Plugins\Core\WordPress\Access;
use COQPIT\Plugins\Core\WordPress\Comments;
use COQPIT\Plugins\Core\WordPress\Emails;
use COQPIT\Plugins\Core\WordPress\Environment;
use COQPIT\Plugins\Core\WordPress\Maintenance;
use COQPIT\Plugins\Core\WordPress\Medias;
use COQPIT\Plugins\Core\WordPress\Robots;
use COQPIT\Plugins\Core\WordPress\Updater;
use COQPIT\Plugins\Core\CustomFields\Menus;
use COQPIT\Plugins\Core\Template\Templates;
use COQPIT\Plugins\Core\Template\StaticPage;
use COQPIT\Plugins\Core\WordPress\Dashboard;
use COQPIT\Plugins\Core\CustomFields\Synchronization;
use COQPIT\Plugins\Core\CustomPostTypes\CustomPostTypes;

class Core {

    public static function init ()
    {

        // Plugin Updates
        //Updater::register(COQPIT_CORE_VERSION, COQPIT_CORE_PLUGIN_FILE);

        //COQPIT Core Option page
        //OptionPage::init();

        // Activation & Deactivation Hooks
        register_activation_hook( COQPIT_CORE_PLUGIN_FILE, [self::class, 'onPluginActivation']);
        register_deactivation_hook( COQPIT_CORE_PLUGIN_FILE, [self::class, 'onPluginDeactivation']);

        // Automatic stuff
        add_action('init', [self::class, 'loadFirst'],      1);
        add_action('init', [self::class, 'startSession'],   1);
        add_action('init', [self::class, 'constants'],      1);
        add_action('init', [self::class, 'updates'],        1);
        add_action('init', [self::class, 'themeSupport'],   1);

        // Register Admin scripts and styles
        Scripts::register('coqpit-core', [
            'assets/css/coqpit-core.css',
            'assets/js/coqpit-core.js'
        ],COQPIT_CORE_URI, COQPIT_CORE_PATH, true);

        Scripts::register('coqpit-core-general', [
            'assets/css/coqpit-core-admin.css',
        ],COQPIT_CORE_URI, COQPIT_CORE_PATH, true);

        // Add Scripts and Styles only on settings page
        /**
        add_filter('core_admin_scripts_registration', function ($value, $scriptName){
            $screen = get_current_screen();
            if($scriptName == 'coqpit-core' && $screen->id != 'settings_page_'.OptionPage::slug()) return false;
            return $value;
        }, 10, 2);
        **/

        // Allow SVG use on media library
        Theme::allowSVG();

        // WordPress's maintenance management
        $maintenance = new Maintenance();
        $maintenance->init();

        // WordPress's environment management
        $environment = new Environment();
        $environment->init();

        // Add new template path for theme
        $templates = new Templates();
        $templates->init();

        // Add custom logic for custom post types, as same as blog page, adding possibility to use pages for custom post types archives
        $customPostTypes = new CustomPostTypes();
        $customPostTypes->init();

        // Automatically add ACF Synchronization to template
        $synchronization = new Synchronization();
        $synchronization->init();

        // Add ACF Rules
        $menus = new Menus();
        $menus->init();

        // Automatically register static pages
        $staticPages = new StaticPage();
        $staticPages->init();

        // Automatically register elementor widgets
        $elementorWidgets = new Widgets();
        $elementorWidgets->init();

        // WordPress's admin dashboard
        $dashboard = new Dashboard();
        $dashboard->init();

        // WordPress's media management
        $medias = new Medias();
        $medias->init();

        // WordPress's comments management
        $comments = new Comments();
        $comments->init();

        // WordPress's emails management
        $emails = new Emails();
        $emails->init();

        // COQPIT Customers access and roles
        $access = new Access();
        $access->init();

        // Check Google indexation for pre-production
        Robots::check();

        // Add auto implementation Tarte au citron JS
        TarteAuCitron::init();

        add_filter('coqpit_core_loaded', function (){
            return true;
        });

    }

    /**
     * Force COQPIT Core to be loaded before all others plugins
     * @return void
     */
    public static function loadFirst()
    {
        $pluginDir = str_replace('\\', '/', WP_PLUGIN_DIR);
        $coreFile = str_replace('\\', '/', COQPIT_CORE_PLUGIN_FILE);
        $path = str_replace( $pluginDir.'/', '', $coreFile );

        if ( $plugins = get_option( 'active_plugins' ) ) {
            if ( $key = array_search( $path, $plugins ) ) {
                array_splice( $plugins, $key, 1 );
                array_unshift( $plugins, $path );
                update_option( 'active_plugins', $plugins );
            }
        }

        // load After ACF Pro if enabled
        if ( $key = array_search( 'advanced-custom-fields-pro/acf.php', $plugins ) ) {
            array_splice( $plugins, $key, 1 );
            array_unshift( $plugins, 'advanced-custom-fields-pro/acf.php' );
            update_option( 'active_plugins', $plugins );
        }
    }

    /**
     * Define WP Constants if not already defined
     * @return void
     */
    public static function constants()
    {
        // Auto update of WordPress's core
        if(!defined('WP_AUTO_UPDATE_CORE')){
            define('WP_AUTO_UPDATE_CORE', false);
        }

        // Remove capability to edit theme and plugins files
        if(!defined('DISALLOW_FILE_EDIT')){
            define('DISALLOW_FILE_EDIT', true);
        }

        // Allow to update or install themes and plugins directly without FTP account (Force)
        if(!defined('FS_METHOD')){
            define( 'FS_METHOD', 'direct');
        }

        // Decrease number of post revisions saved
        if(!defined('WP_POST_REVISIONS')){
            define( 'WP_POST_REVISIONS', 5);
        }

        // Decrease time during elements are stored in trash
        if(!defined('EMPTY_TRASH_DAYS')){
            define( 'EMPTY_TRASH_DAYS', 7);
        }

        // Allow user to use trash functionality for medias
        if(!defined('MEDIA_TRASH')){
            define( 'MEDIA_TRASH', true);
        }
    }

    /**
     * Disable automatic updates
     * @return void
     */
    public static function updates()
    {
        // Disable auto update of plugins
        add_filter( 'auto_update_plugin', '__return_false' );

        // Disable auto update of themes
        add_filter( 'auto_update_theme', '__return_false' );
    }

    /**
     * Start PHP Session if not exists
     * @return void
     */
    public static function startSession()
    {
        if(!session_id()) {
            session_start();
        }
    }

    /**
     * Add automatic theme supports
     * @return void
     */
    public static function themeSupport()
    {
        add_theme_support('custom-logo');
        add_theme_support('post-thumbnails');
        add_theme_support('menus');
        add_theme_support('woocommerce');
    }

    /**
     * Method triggered only when plugin was activated
     * @return void
     */
    public static function onPluginActivation()
    {
        // Automatically define permalink structure
        update_option('permalink_structure', '/%postname%/');
        // Automatically add admin email
        update_option('admin_email', 'contact@coqpit.fr');
        // Automatically add Webp options
        update_option('coqpit_webp_optimization_enabled', 'on');
        update_option('coqpit_webp_optimization', 30);
        // Automatically add Comments enabled option
        update_option('coqpit_use_comments', '');
    }

    /**
     * Method triggered only when plugin was deactivated
     * @return void
     */
    public static function onPluginDeactivation()
    {
        delete_option('coqpit_webp_optimization_enabled');
        delete_option('coqpit_webp_optimization');
        delete_option('coqpit_use_comments');
    }

}
