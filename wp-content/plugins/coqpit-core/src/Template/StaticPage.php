<?php

namespace COQPIT\Plugins\Core\Template;

use COQPIT\Core\Vendor\Illuminate\Support\Str;
use COQPIT\Plugins\Core\Traits\HasWPML;

class StaticPage {

    use HasWPML;

    public $pages = [];

    /**
     * Add custom static page
     * @return void
     */
    public static function add($option, $name, $templateName, $position = 1) :void
    {
        add_filter('coqpit_static_pages', function ($pages) use ($option, $name, $templateName){
            $pages[] = [
                'option'    => $option,
                'name'      => $name,
                'template'  => $templateName
            ];
            return $pages;
        }, $position, 1);
    }

    /**
     * Wait before theme was setup
     * @return void
     */
    public function init() :void
    {
        add_action('after_setup_theme', [$this, 'register']);
    }

    /**
     * Register all static pages
     * @return void
     */
    public function register() :void
    {
        $this->pages = apply_filters('coqpit_static_pages', $this->pages);

        if(is_array($this->pages) && count($this->pages)){
            add_action('init',                                  [$this, 'onOptionUpdate']);
            add_action('admin_init',                            [$this, 'readingOptions']);
            add_filter('display_post_states',                   [$this, 'displayPageName']);
            add_filter('wp_setup_nav_menu_item',                [$this, 'addCustomPageLabels'], 1, 2 );
            add_filter('page_template_hierarchy',               [$this, 'addPageTemplate'], 5, 1);
            add_action('edit_form_after_title',                 [$this, 'addNotices']);
            add_filter('body_class',                            [$this, 'addBodyClass']);

            // ACF
            add_filter('acf/location/rule_values/page_type',    [$this, 'addACFPageTypeValue']);
            add_filter('acf/location/rule_match/page_type',     [$this, 'addACFPageTypeMatch'], 10, 4);
        }
    }

    /**
     * Check option value before update
     * @return void
     */
    public function onOptionUpdate()
    {
        foreach ($this->pages as $page){
            add_filter('pre_update_option_'.$page['option'], [$this, 'alterPageOptionBeforeUpdate'], 10, 3);
        }
    }

    /**
     * Add reading option selectors in admin
     * @return void
     */
    public function readingOptions() :void
    {

        add_settings_section( 'page_for_static_pages', __( 'Pages statiques', 'coqpit-core' ), '__return_false', 'reading' );

        foreach ($this->pages as $page){

            $option = $page['option'];
            $name = $page['name'];

            register_setting('reading', $option, [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => NULL,
            ]);

            $optionID = $this->getOptionPageID($option);

            add_settings_field($option, $name, function ($args) use ($option, $name, $optionID){

                echo wp_dropdown_pages([
                    'name'              => $option,
                    'echo'              => 0,
                    'show_option_none'  => __( '&mdash; Select &mdash;' ),
                    'option_none_value' => '0',
                    'selected'          => $optionID,
                ]);

            }, 'reading', 'page_for_static_pages', ['label_for' => $option]);
        }
    }

    /**
     * Display page option name in list of pages
     * @return array
     */
    public function displayPageName($states) :array
    {
        global $post;

        if(empty($post)) return $states;

        foreach ($this->pages as $page) {

            $pageID = $this->getOptionPageID($page['option']);

            if ($post && 'page' == get_post_type($post->ID) && $post->ID == $pageID && $pageID != '0') {
                $states[$page['option']] = $page['name'];
            }

        }

        return $states;
    }

    /**
     * Rename menu item box when using a static page
     *
     * @param $menuItem
     * @return mixed
     */
    public function addCustomPageLabels ($menuItem)
    {
        foreach ($this->pages as $page){
            $pageID = $this->getOptionPageID($page['option']);
            if($pageID && $pageID == $menuItem->object_id){
                $menuItem->type_label = $page['name'];
            }
        }

        return $menuItem;
    }

    /**
     * Create custom template page based on static page template name
     * @param $templates
     * @return mixed
     */
    public function addPageTemplate ($templates)
    {
        global $post;

        if(empty($post)) return $templates;

        foreach ($this->pages as $page){
            $pageID = $this->getOptionPageID($page['option']);
            if($post->ID == $pageID){
                array_unshift($templates, 'templates/'.$page['template'].'.php');
            }
        }

        return $templates;
    }

    /**
     * Display notice on custom post types pages
     * @return void
     */
    public function addNotices() :void
    {
        global $post;

        if(!empty($post)) {
            foreach ($this->pages as $page) {

                $pageID = $this->getOptionPageID($page['option']);

                if ('page' == get_post_type($post->ID) && $post->ID == $pageID && $pageID != '0') {
                    $class = 'notice notice-warning inline';
                    $message = sprintf(__('Vous êtes en train de modifier la page "%s".', 'coqpit-core'), mb_strtolower($page['name']));

                    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
                }
            }
        }
    }

    /**
     * Add class to body for specific pages
     * @param array $classes
     * @return array
     */
    public function addBodyClass(array $classes) :array
    {
        global $post;

        if(empty($post)) return $classes;

        foreach ($this->pages as $page){

            $pageID = $this->getOptionPageID($page['option']);

            if('page' == get_post_type($post->ID) && $post->ID == $pageID && $pageID != '0'){
                $classes[] = Str::slug($page['option']);
                $classes[] = Str::slug($page['name']);
            }
        }

        return $classes;

    }

    /**
     * Add ACF Page type for Fields groups
     *
     * @param $choices
     * @return mixed
     */
    public function addACFPageTypeValue ($choices)
    {
        foreach ($this->pages as $page){
            $choices[$page['option']] = $page['name'];
        }

        return $choices;
    }

    /**
     * Add additional check for ACF Page type when fields are displayed
     *
     * @param $choices
     * @return mixed
     */
    public function addACFPageTypeMatch ($match, $rule, $options, $field_group)
    {

        global $post;

        if(empty($post)) return $match;

        foreach ( $this->pages as $page ){
            if ($rule['value'] != $page['option']) {
                continue;
            }

            $pageID = $this->getOptionPageID($page['option']);

            if ( $rule['operator'] == "==" ) {
                $match =
                    (
                        get_option($page['option']) == $post->ID
                        || ( ($pageID && '0' != $pageID) && $pageID == $post->ID )
                    );
            }
            elseif ( $rule['operator'] == "!=" ) {
                $match =
                    (
                        get_option($page['option']) != $post->ID
                        || ( ($pageID && '0' != $pageID) && $pageID != $post->ID )
                    );
            }
            
        }

        return $match;

    }

    /**
     * If WPML is active, check current language and define source language as page option value
     * @param $value
     * @param $oldValue
     * @param $option
     * @return mixed
     */
    public function alterPageOptionBeforeUpdate ($value, $oldValue, $option){

        if($this->getCurrentLanguage()){
            $currentLanguage = $this->getCurrentLanguage();
            $translations = $this->getTranslations($value);
            $currentTranslation = (isset($translations[$currentLanguage])) ? $translations[$currentLanguage] : null;

            if($currentTranslation && $currentTranslation->source_language_code){
                $value = (isset($translations[$currentTranslation->source_language_code])) ? $translations[$currentTranslation->source_language_code]->element_id : $value;
            }
        }

        return $value;

    }

}
