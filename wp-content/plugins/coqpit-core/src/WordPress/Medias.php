<?php

namespace COQPIT\Plugins\Core\WordPress;

use COQPIT\Plugins\Core\Utils\Browser;

class Medias {

    public function init()
    {
        add_action('admin_menu',                            [$this, 'controlMediaAccess']);
        add_action('admin_init',                            [$this, 'mediasOptions']);
        add_action('delete_attachment',                     [$this, 'removeWebpMedias']);
        add_filter('wp_generate_attachment_metadata',       [$this, 'generateWebpMediaSizes'], 20, 3);
        add_filter('wp_get_attachment_metadata',            [$this, 'rewriteAttachmentMeta'], 10, 2);
        add_filter('wp_get_attachment_url',                 [$this, 'rewriteAttachmentUrl'], 10, 2);
        add_filter('wp_get_attachment_image_attributes',    [$this, 'addDraggableFalse'], 10, 1);
    }

    public function controlMediaAccess()
    {
        if(!current_user_can('edit_medias_settings')) {
            remove_submenu_page('options-general.php', 'options-media.php');
        }
    }

    public function mediasOptions()
    {
        if(current_user_can('edit_medias_settings')) {

            add_settings_section('coqpit-medias-optimizations', __('Optimisations (Webp)', 'coqpit-core'), '__return_false', 'media');

            register_setting('media', 'coqpit_webp_optimization_enabled', [
                'type' => 'boolean',
                'default' => true,
            ]);

            add_settings_field('coqpit-webp-optimization-enabled-field', __('Activer l\'utilisation des fichiers Webp', 'coqpit-core'), [$this, 'renderEnabledSetting'], 'media', 'coqpit-medias-optimizations', [
                'label_for' => 'coqpit_webp_optimization_enabled'
            ]);

            register_setting('media', 'coqpit_webp_optimization', [
                'type' => 'integer',
                'default' => 60,
            ]);

            add_settings_field('coqpit-webp-optimization-field', __('Qualité des images Webp', 'coqpit-core'), [$this, 'renderQualitySetting'], 'media', 'coqpit-medias-optimizations', [
                'label_for' => 'coqpit_webp_optimization'
            ]);

        }
    }

    public function renderEnabledSetting ()
    {
        $enabled = get_option( 'coqpit_webp_optimization_enabled' );
        $checked = ($enabled) ? ' checked="checked"' : '';
        echo '<label>';
        echo '<input id="coqpit-webp-optimization-enabled-field" name="coqpit_webp_optimization_enabled" type="checkbox"'.$checked.'/>';
        echo '<p style="max-width: 400px">'.__('Active ou désactive l\'utilisation automatisée des fichiers webp dans le thème', 'coqpit-core').'</p>';
        echo '</label>';
    }

    public function renderQualitySetting ()
    {
        $quality = get_option( 'coqpit_webp_optimization' );
        echo '<input id="coqpit-webp-optimization-field" class="small-text" name="coqpit_webp_optimization" type="number" min="0" max="100" value="' . $quality . '" /> %';
        echo '<p style="max-width: 400px">'.__('Permet de définir la qualité des images générées en webp à partir des images png ou jpg déposées dans la librairie de médias', 'coqpit-core').'</p>';
    }

    public function addDraggableFalse($attributes)
    {
        $attributes['draggable'] = 'false';
        return $attributes;
    }

    public function rewriteAttachmentMeta( $data, $attachmentID )
    {
        $enabled = get_option( 'coqpit_webp_optimization_enabled' );
        $isElementorEditor = (is_plugin_active('elementor/elementor.php') && isset($_GET['action']) && $_GET['action'] == 'elementor');

        if($this->isValidBrowser() && isset($data['file'])) {
            if($enabled && (!is_admin()) || $isElementorEditor && (isset($data['mime_type']) && ($data['mime_type'] == 'image/png' || $data['mime_type'] == 'image/jpg' || $data['mime_type'] == 'image/jpeg'))) {
                $uploadDir = wp_upload_dir();
                $webpFile = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $data['file']);

                if(file_exists($uploadDir['basedir'].'/'.$webpFile)){

                    $data['file'] = $webpFile;

                    foreach($data['sizes'] as &$size){
                        $defaultDir = pathinfo($data['file'], PATHINFO_DIRNAME);
                        $newFile = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $size['file']);
                        $filePath = $uploadDir['basedir'].'/'.$defaultDir.'/'.$newFile;
                        if(file_exists($filePath)){
                            $size['file'] = $newFile;
                        }
                    }

                }
            }
        }

        return $data;
    }

    public function rewriteAttachmentUrl($url, $attachmentID)
    {

        $enabled = get_option( 'coqpit_webp_optimization_enabled' );
        $isElementorEditor = (is_plugin_active('elementor/elementor.php') && isset($_GET['action']) && $_GET['action'] == 'elementor');

        if($this->isValidBrowser()) {
            if ($enabled && (!is_admin() || $isElementorEditor)) {
                $uploadDir = wp_upload_dir();
                $webpFile = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $url);
                if (file_exists(str_replace($uploadDir['baseurl'], $uploadDir['basedir'], $webpFile))) {
                    return $webpFile;
                }
            }
        }

        return $url;
    }

    public function isValidBrowser()
    {
        $browser = new Browser();
        
        $browserName = $browser->getBrowser();
        if($browserName != 'Safari') return true;

        $browserVersion = $browser->getVersion();
        if($browserVersion >= 16.5) return true;

        return false;
    }

    public function generateWebpMediaSizes($metaData, $attachmentID, $context)
    {
        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        $uploadDirectory = wp_upload_dir();
        $filePath = $uploadDirectory['basedir'].'/'.$metaData['file'];
        $fileDirectory = pathinfo($filePath, PATHINFO_DIRNAME);
        $mimeType = mime_content_type($filePath);
        if(in_array($mimeType, $allowedMimeTypes)) {
            $file = $this->generateWebpMedia($filePath);
            if($file) $metaData['image_meta']['webp'] = $file;

            if(isset($metaData['sizes']) && is_array($metaData['sizes']) && count($metaData['sizes'])) {
                foreach($metaData['sizes'] as $key => $size) {
                    $filePath = $fileDirectory.'/'.$size['file'];
                    $file = $this->generateWebpMedia($filePath);
                    $newFileData = pathinfo($file);
                    if($file) $metaData['sizes'][$key]['webp'] = $newFileData['basename'];
                }
            }
        }

        return $metaData;
    }

    public function generateWebpMedia($filePath)
    {
        $informations = pathinfo($filePath);
        $fileMime     = mime_content_type($filePath);
        $newPath      = $informations['dirname'] . '/' . $informations['filename'] . '.webp';

        switch($fileMime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filePath);

                if( $image === false ){
                    return false;
                }

                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            default:
                return false;
        }

        imagewebp($image, $newPath, (get_option('coqpit_webp_optimization')) ?: 60);
        imagedestroy($image);

        return $this->removeBaseDir($newPath);
    }

    public function removeWebpMedias($postID)
    {
        $uploadDirectory = wp_upload_dir();
        $metaData = wp_get_attachment_metadata($postID);

        if(isset($metaData['sizes']) && is_array($metaData['sizes']) && count($metaData['sizes'])) {
            foreach($metaData['sizes'] as $size) {
                $file = $uploadDirectory['path'].'/'.$size['webp'];
                if(file_exists($file)) {
                    unlink($file);
                }
            }
        }

        $file = $uploadDirectory['basedir'].'/'.$metaData['image_meta']['webp'];

        if(file_exists($file)) {
            unlink($file);
        }
    }

    public function removeBaseDir($file)
    {
        $uploadDirectory = wp_upload_dir();
        $file = str_replace($uploadDirectory['basedir'].'/', '', $file);
        return $file;
    }

}
