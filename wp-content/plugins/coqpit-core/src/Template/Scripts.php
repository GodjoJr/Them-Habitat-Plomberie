<?php

namespace COQPIT\Plugins\Core\Template;

use COQPIT\Core\Vendor\Illuminate\Support\Str;

class Scripts {

    public string $name = 'template';
    public string|null $url = null;
    public string|null $path = null;
    public array $elements = [];
    public array $scripts = [];
    public array $styles = [];
    public bool $withHotReload = false;

    public static function register(string $name, array $elements = [], string $url = null, string $path = null, bool $withHotReload = false)
    {
        $object = (new self);
        $object->name = $name;
        $object->url = ($url) ? $url.'/' : get_template_directory_uri();
        $object->path = ($path) ?: get_template_directory();
        $object->elements = $elements;
        $object->withHotReload = $withHotReload;

        $object->checkHotReload();
        $object->convertScripts();

        add_action( 'wp_enqueue_scripts', [$object, 'registerScripts']);
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
                $this->url = 'http://localhost:'.COQPIT_WATCHER_PORT.'/';
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
            if(is_array($element)) {
                $url = $this->url.$element[0];
                $current = pathinfo($element[0]);
                $dependencies = (isset($element[1]) && is_array($element[1])) ? $element[1] : [];
                $version = $this->getFileSizeVersion($this->path.'/'.$element[0]);
            } else if(is_string($element)) {
                $url = $this->url.$element;
                $current = pathinfo($element);
                $dependencies = [];
                $version = $this->getFileSizeVersion($this->path.'/'.$element);
            }

            if($current['extension'] == 'css'){

                $this->styles[] = [
                    'element'       => $element,
                    'slug'          => Str::slug($this->name.'-'.$current['filename'].'-style'),
                    'url'           => $url,
                    'dependencies'  => $dependencies,
                    'version'       => $version
                ];
            }

            if($current['extension'] == 'js'){
                $this->scripts[] = [
                    'element'       => $element,
                    'ajax'          => Str::camel($current['filename'].'-script').'Ajax',
                    'slug'          => Str::slug($this->name.'-'.$current['filename'].'-script'),
                    'url'           => $url,
                    'dependencies'  => $dependencies,
                    'version'       => $version
                ];
            }
        }

        foreach ($this->styles as $key => $style){
            if(isset($this->styles[$key - 1])){
                $this->styles[$key]['dependencies'] = [...$this->styles[$key]['dependencies'], ...[$this->styles[$key - 1]['slug']]];
            }
        }

        foreach ($this->scripts as $key => $script){
            if(isset($this->scripts[$key - 1])){
                $this->scripts[$key]['dependencies'] = [...$this->scripts[$key]['dependencies'], ...[$this->scripts[$key - 1]['slug']]];
            }
        }
    }

    /**
     * Get file size and use as version to avoid cache if file was rebuild
     * @param $file
     * @return mixed
     */
    public function getFileSizeVersion ($file) :mixed
    {

        if(file_exists($file)){
            return filesize($file);
        }

        return '1.0.0';
    }

    /**
     * WordPress enqueue scripts and styles base function
     * @return void
     */
    public function registerScripts() :void
    {
        $loadStyles = apply_filters('core_scripts_registration', true, $this->name);

        if($loadStyles){
            foreach ($this->styles as $style){
                wp_enqueue_style($style['slug'], $style['url'], $style['dependencies'], $style['version']);
            }

            foreach ($this->scripts as $script){
                wp_enqueue_script($script['slug'], $script['url'], $script['dependencies'], $script['version'], true);
                do_action('after_coqpit_core_script_enqueue', $script);
            }
        }

    }

    /**
     * Enable ajax and localize script(s)
     * @return void
     */
    public static function ajax(array|string $scriptPaths = []) :void
    {
        add_action('after_coqpit_core_script_enqueue', function ($script) use ($scriptPaths){
            if(is_array($scriptPaths)){
                foreach($scriptPaths as $path){
                    if($path === $script['element']){
                        wp_localize_script($script['slug'], $script['ajax'], [
                            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                            'nonce'     => wp_create_nonce()
                        ]);
                    }
                }
            } else {
                if($scriptPaths === $script['element']){
                    wp_localize_script($script['slug'], $script['ajax'], [
                        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                        'nonce'     => wp_create_nonce()
                    ]);
                }
            }
        });
    }

}