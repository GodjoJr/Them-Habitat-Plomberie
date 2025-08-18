<?php

namespace COQPIT\Plugins\Core\Template;

use COQPIT\Core\Vendor\Illuminate\Support\Str;

class Vite {

    protected string $server = 'http://localhost';
    protected string $port = '5173';
    protected array $scripts = [];
    protected array $styles = [];

    public function __construct(
        public string|null $entrypoint = null,
        public string|null $manifest = null,
        public bool $withAjax = false,
    ) {

        $this->server = (defined('WP_VITE_SERVER')) ? WP_VITE_SERVER : $this->server;
        $this->server = apply_filters('coqpit_vite_server_url', $this->server);
        $this->port = (defined('WP_VITE_PORT')) ? WP_VITE_PORT : $this->port;
        $this->port = apply_filters('coqpit_vite_server_port', $this->port);

        if($this->isWatcherOnline()){
            $this->retrieveDevelopmentAssets();
            add_action( 'wp_enqueue_scripts', [$this, 'enqueueDevelopmentAssets']);
            add_filter('script_loader_tag', [$this, 'addModuleToScriptTags'], 10, 3);
        } else {
            $this->retrieveProductionAssets();
            add_action( 'wp_enqueue_scripts', [$this, 'enqueueProductionAssets']);
        }

    }

    public static function register(string $entrypoint, string $manifest, bool $ajax = false) : void {
        new self(entrypoint: $entrypoint, manifest: $manifest, withAjax: $ajax);
    }

    private function retrieveProductionAssets() :void
    {
        if(file_exists($this->manifest)){
            $scripts = json_decode(file_get_contents($this->manifest), true);
            $directory = pathinfo($this->manifest, PATHINFO_DIRNAME);

            foreach($scripts as $script){
                $this->scripts[] = [
                    'ajax'          => Str::camel($script['name'].'-script').'Ajax',
                    'file'          => $directory.'/'.$script['file'],
                    'slug'          => 'main-script',
                    'url'           => str_replace(get_template_directory(), get_template_directory_uri(), $directory).'/'.$script['file'],
                    'dependencies'  => [],
                    'version'       => '1.0.0'
                ];

                if(isset($script['css']) && is_array($script['css']) && count($script['css'])){
                    foreach($script['css'] as $css){
                        $this->styles[] = [
                            'file'          => $directory.'/'.$css,
                            'slug'          => 'main-style',
                            'dependencies'  => [],
                            'url'           => str_replace(get_template_directory(), get_template_directory_uri(), $directory).'/'.$css,
                            'version'       => '1.0.0'
                        ];
                    }
                }
            }
        }
    }

    private function retrieveDevelopmentAssets() :void
    {

        $fileName = pathinfo($this->entrypoint, PATHINFO_FILENAME);
        $file = pathinfo($this->entrypoint, PATHINFO_BASENAME);
        $directory = pathinfo($this->entrypoint, PATHINFO_DIRNAME);

        $this->scripts[] = [
            'file'          => null,
            'slug'          => 'local-vite-script',
            'url'           => $this->server.':'.$this->port.'/@vite/client',
            'dependencies'  => [],
            'version'       => '1.0.0',
            'footer'        => false
        ];

        $this->scripts[] = [
            'ajax'          => Str::camel($fileName.'-script').'Ajax',
            'file'          => $directory.'/'.$file,
            'slug'          => 'local-main-script',
            'url'           => str_replace(get_template_directory(), $this->server.':'.$this->port, $directory).'/'.$file,
            'dependencies'  => [],
            'version'       => '1.0.0',
            'footer'        => true
        ];
    }


    private function isWatcherOnline() :bool
    {
        $request = wp_remote_get($this->server.':'.$this->port.'/@vite/client', ['sslverify' => false]);
        return (!is_wp_error($request) && $request['response']['code'] == 200);
    }

    public function enqueueProductionAssets() :void
    {
        foreach ($this->styles as $style){
            wp_enqueue_style($style['slug'], $style['url'], $style['dependencies'], $style['version']);
        }

        foreach ($this->scripts as $script){
            wp_enqueue_script($script['slug'], $script['url'], $script['dependencies'], $script['version'], true);

            if($this->withAjax && isset($script['ajax'])){
                wp_localize_script($script['slug'], $script['ajax'], [
                    'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                    'nonce'     => wp_create_nonce()
                ]);
            }
        }
    }

    public function enqueueDevelopmentAssets() :void
    {
        foreach ($this->scripts as $script){
            wp_enqueue_script($script['slug'], $script['url'], $script['dependencies'], $script['version'], $script['footer']);

            if($this->withAjax && isset($script['ajax'])){
                wp_localize_script($script['slug'], $script['ajax'], [
                    'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                    'nonce'     => wp_create_nonce()
                ]);
            }
        }
    }

    public function addModuleToScriptTags($tag, $handle, $src) :string
    {
        if ( 'local-vite-script' == $handle || 'local-main-script' == $handle) {
            $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
        }

        return $tag;
    }
}
