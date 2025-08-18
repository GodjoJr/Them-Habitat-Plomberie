<?php

namespace COQPIT\Plugins\Core\Admin;

use COQPIT\Core\Vendor\Illuminate\Support\Str;

class Scripts {

    public string $name = 'template';
    public string $version = '1.0.0';
    public string|null $url = null;
    public string|null $path = null;
    public array $elements = [];
    public array $scripts = [];
    public array $styles = [];
    public bool $withHotReload = false;

    public static function register(string $name, array $elements = [], string $url = null, string $path = null, bool $withHotReload = false, string $version = '1.0.0')
    {

        $object = (new self);
        $object->name = $name;
        $object->url = ($url) ?: get_template_directory_uri();
        $object->path = ($path) ?: get_template_directory();
        $object->version = $version;
        $object->elements = $elements;
        $object->withHotReload = $withHotReload;

        $object->checkHotReload();
        $object->convertScripts();

        add_action( 'admin_enqueue_scripts', [$object, 'registerScripts']);
    }

    /**
     * Check if hot reload is active and transform files URL if needed
     * @return void
     */
    public function checkHotReload() :void
    {
        if($this->withHotReload){

            if(!defined('COQPIT_WATCHER_PORT')){
                define('COQPIT_WATCHER_PORT', '8080');
            }

            if (file_exists($this->path.'/hot')){
                $this->url = 'http://localhost:'.COQPIT_WATCHER_PORT;
            }
        }
    }

    /**
     * Split Styles and scripts and magically add dependencies based on array position
     * @return void
     */
    public function convertScripts() :void
    {
        foreach ($this->elements as $element){
            $current = pathinfo($element);

            if($current['extension'] == 'css'){

                $this->styles[] = [
                    'slug'          => Str::slug($this->name.'-'.$current['filename'].'-style'),
                    'url'           => $this->url.'/'.$element,
                    'dependencies'  => []
                ];
            }

            if($current['extension'] == 'js'){
                $this->scripts[] = [
                    'ajax'          => Str::camel($current['filename'].'-script').'Ajax',
                    'slug'          => Str::slug($this->name.'-'.$current['filename'].'-script'),
                    'url'           => $this->url.'/'.$element,
                    'dependencies'  => []
                ];
            }
        }

        foreach ($this->styles as $key => $style){
            if(isset($this->styles[$key - 1])){
                $this->styles[$key]['dependencies'] = [$this->styles[$key - 1]['slug']];
            }
        }

        foreach ($this->scripts as $key => $script){
            if(isset($this->scripts[$key - 1])){
                $this->scripts[$key]['dependencies'] = [$this->scripts[$key - 1]['slug']];
            }
        }
    }

    /**
     * WordPress enqueue scripts and styles base function
     * @return void
     */
    public function registerScripts() :void
    {

        $loadStyles = apply_filters('core_admin_scripts_registration', true, $this->name);

        if($loadStyles){
            foreach ($this->styles as $style){
                wp_enqueue_style($style['slug'], $style['url'], $style['dependencies'], $this->version);
            }

            foreach ($this->scripts as $script){
                wp_enqueue_script($script['slug'], $script['url'], $script['dependencies'], $this->version, true);
                wp_localize_script($script['slug'], $script['ajax'], [
                    'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                    'nonce'     => wp_create_nonce($script['slug'].'-nonce')
                ]);
            }
        }

    }

}
