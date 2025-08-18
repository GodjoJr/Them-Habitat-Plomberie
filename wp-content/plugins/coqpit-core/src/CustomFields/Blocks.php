<?php

namespace COQPIT\Plugins\Core\CustomFields;


class Blocks {

    public $uri = '';
    public $path = '';
    public $hasHotReload = false;
    public $mainScriptPath = false;
    public $blocks = [];
    public $categories = [];

    /**
     * Automatically register ACF Gutenberg Blocks in template
     *
     * @param $path
     * @return void
     */
    public static function autoRegisterBlocks ($path = '/blocks', $mainScriptPath = false) :void
    {
        $self = (new self);
        $self->path = get_template_directory().$path;
        $self->uri = get_template_directory_uri().$path;
        $self->mainScriptPath = $mainScriptPath;
        $directoryContent = scandir($self->path);
        $self->blocks = array_diff($directoryContent, ['.', '..']);
        $self->checkHotReload();
        add_action('init', [$self, 'registration']);
    }

    /**
     * Check if hot reload is active and transform files URL if needed
     * @return void
     */
    public function checkHotReload() :void
    {
        if (file_exists(get_template_directory().'/hot')){
            $this->uri = str_replace(get_template_directory_uri(), 'http://localhost:8080', $this->uri);
            $this->hasHotReload = true;
        }
    }

    /**
     * Add New Gutenberg blocks category
     * @param $slug
     * @param $name
     * @return void
     */
    public static function addBlockCategory($slug, $name) :void
    {
        $self = (new self);
        $self->categories[] = [
            'slug'    => $slug,
            'title'   => $name
        ];

        add_filter( 'block_categories_all' , [$self, 'registerCategory']);
    }

    public function registration () :void
    {
        if(is_admin() && $this->hasHotReload){
            add_action( 'admin_enqueue_scripts', [$this, 'loadMainScript'] );
        }

        foreach ($this->blocks as $block){
            wp_register_style('block-'.$block, $this->uri.'/'.$block.'/block.css');
            register_block_type($this->path.'/'.$block);
        }
    }

    public function loadMainScript () :void
    {
        wp_enqueue_script( 'main-app-script', 'http://localhost:8080'.$this->mainScriptPath, [], '1.0' );
    }

    /**
     * Register categories from wp hook "block_categories_all"
     * @param $categories
     * @return array
     */
    public function registerCategory ($categories) :array
    {
        foreach ($this->categories as $category){
            array_unshift($categories, $category);
        }

        return $categories;
    }

}
