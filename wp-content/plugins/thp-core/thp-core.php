<?php
/**
 * Plugin Name: THP - Core
 * Description: Plugin personnalisé pour Therm Habitat Plomberie
 * Version: 1.0.0
 * Author: COQPIT - Agence Digitale
 * Author URI: https://www.coqpit.fr
 * Text Domain: thp-core
 * Requires at least: 6.2
 * Requires PHP: 8.1
 */

defined( 'ABSPATH' ) || exit;

// Define necessary constants
define('THP_CORE_VERSION', '1.0.0');
define('THP_CORE_PATH', plugin_dir_path(__FILE__));
define('THP_CORE_URI', plugin_dir_url(__FILE__));
define('THP_CORE_FILE', __FILE__ );

// Dependencies autoload
require_once __DIR__.'/vendor/autoload.php';

// Helpers  
require_once __DIR__ . '/helpers/helpers.php';

// Main classes autoload
if(file_exists(__DIR__.'/composer.json')){
    $composerConfiguration = json_decode(file_get_contents(__DIR__.'/composer.json'), true);
    $packages = (isset($composerConfiguration['autoload']) && isset($composerConfiguration['autoload']['psr-4'])) ? $composerConfiguration['autoload']['psr-4'] : [];
}

if ( is_array( $packages ) ) {
    spl_autoload_register(
        function ( $classname ) use ($packages) {
            foreach ($packages as $namespace => $directory){
                $file = str_replace('\\', '/', str_replace($namespace, $directory, $classname)).'.php';
                $filePath = __DIR__.'/'.$file;
                if ( file_exists( $filePath ) ) {
                    require_once $filePath;
                }
            }
        }
    );
}

if(apply_filters('coqpit_core_loaded', null)){
    \COQPIT\Plugins\ThpCore\THPCore::init();
}

