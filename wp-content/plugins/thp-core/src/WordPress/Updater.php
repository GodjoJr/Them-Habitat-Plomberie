<?php

namespace COQPIT\Plugins\ThpCore\WordPress;

class Updater {

    public $pluginVersion;
    public $pluginName;
    public $pluginSlug;

    private $transientKey;
    private $url = 'https://plugins.coqpit.fr/V6QapaZrZm5VZa/{slug}/info.json';

    public static function register($version, $plugin)
    {
        $plugin = pathinfo(plugin_basename($plugin));

        $updater = new self();
        $updater->pluginVersion = $version;
        $updater->pluginName = $plugin['dirname'].'/'.$plugin['basename'];
        $updater->pluginSlug = $plugin['filename'];
        $updater->transientKey = md5( sanitize_key( $updater->pluginName ) . 'response_transient' );
        $updater->url = str_replace('{slug}', $plugin['dirname'], $updater->url);

        add_filter('pre_set_site_transient_update_plugins', [$updater, 'checkForUpdate'], 10, 1);
        add_action('delete_site_transient_update_plugins',  [$updater, 'deleteTransients']);
        add_filter('plugins_api',                           [$updater, 'informations'], 20, 3);

        $updater->maybeDeleteTransients();
    }

    public function deleteTransients()
    {
        delete_option( $this->transientKey );
    }

    public function getPluginInformations(){

        $remote = wp_remote_get($this->url, [
            'timeout' => 10,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        if(is_wp_error($remote) || 200 !== wp_remote_retrieve_response_code($remote) || empty(wp_remote_retrieve_body($remote))) {
            return false;
        }

        $remote = json_decode(wp_remote_retrieve_body($remote));

        return $remote;

    }

    /**
     * Get plugin's information from distant directory
     * @return \stdClass
     */
    public function informations($response, $action, $args)
    {
        if('plugin_information' !== $action) {
            return $response;
        }

        if (!isset($args->slug) || ($args->slug !== $this->pluginSlug) ) {
            return $response;
        }

        $cacheKey = 'coqpit_api_request_' . substr(md5(serialize($this->pluginSlug)), 0, 15);
        $apiRequestTransient = get_site_transient($cacheKey);

        if(empty($apiRequestTransient)){
            $informations = $this->getPluginInformations();

            if (!$informations || is_wp_error($informations)) {
                return $response;
            }

            $response = new \stdClass();
            $response->name = $informations->name;
            $response->slug = $informations->slug;
            $response->version = $informations->version;
            $response->tested = $informations->tested;
            $response->requires = $informations->requires;
            $response->author = $informations->author;
            $response->download_link = $informations->download_url;
            $response->trunk = $informations->download_url;
            $response->requires_php = $informations->requires_php;
            $response->last_updated = $informations->last_updated;
            $response->sections = [
                'description' => $informations->sections->description
            ];

            if( ! empty( $informations->banners ) ) {
                $response->banners = [
                    'low' => $informations->banners->low,
                    'high' => $informations->banners->high
                ];
            }

            set_site_transient( $cacheKey, $response, DAY_IN_SECONDS );
        }

        return $response;
    }

    public function checkForUpdate($transient){

        if (!is_object($transient)) {
            $transient = new \stdClass();
        }

        return $this->checkTransientData($transient);

    }

    public function checkTransientData( $transient ){

        if (!is_object($transient)) {
            $transient = new \stdClass();
        }

        $informations = $this->getPluginInformations();

        // If informations request has error
        if (!$informations || is_wp_error($informations)) {
            return $transient;
        }

        // If WordPress version not satisfied
        if(!version_compare($informations->requires, get_bloginfo( 'version' ), '<=')) {
            return $transient;
        }

        // If PHP version not satisfied
        if(!version_compare($informations->requires_php, PHP_VERSION, '<')) {
            return $transient;
        }

        if (version_compare($this->pluginVersion, $informations->version, '<')) {
            $transient->response[$this->pluginName] = $informations;
            $transient->checked[$this->pluginName] = $informations->version;
        } else {
            $transient->no_update[$this->pluginName] = $informations;
            $transient->checked[$this->pluginName] = $this->pluginVersion;
        }

        $transient->last_checked = current_time('timestamp');

        return $transient;

    }

    private function maybeDeleteTransients() {
        global $pagenow;

        if ( 'update-core.php' === $pagenow && isset( $_GET['force-check'] ) ) {
            $this->deleteTransients();
        }
    }

}
