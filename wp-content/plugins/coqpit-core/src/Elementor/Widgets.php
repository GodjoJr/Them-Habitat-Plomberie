<?php

namespace COQPIT\Plugins\Core\Elementor;

class Widgets {

    public $path;
    public $widgets = [];

    public function __construct(){
        $this->path = get_template_directory().'/elementor/widgets';
        if(file_exists($this->path)){
            $directoryContent = scandir($this->path);
            $this->widgets = array_diff($directoryContent, ['.', '..']);
        }
    }

    public function init()
    {
        add_action('elementor/widgets/register', [$this, 'registerWidgets']);
    }

    public function registerWidgets($widgets_manager)
    {
        foreach ($this->widgets as $key => $widget){
            $object = str_replace('.php', '', $widget);
            include_once($this->path.'/'.$widget);
            $widgets_manager->register(new $object());
        }
    }

}
