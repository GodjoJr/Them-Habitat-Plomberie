<?php

namespace COQPIT\Plugins\Core\CustomPostTypes;

use COQPIT\Plugins\Core\Template\Templates;
use COQPIT\Plugins\Core\Traits\HasWPML;

class CustomPostTypes {

    use HasWPML;

    public string $optionPrefix;
    public array $postTypes = [];
    public array $excludedPostTypes = [
        'post', 'page', 'attachment', 'revision', 'nav_menu_item',
        'custom_css', 'customize_changeset', 'oembed_cache', 'user_request',
        'wp_block', 'wp_template', 'wp_template_part', 'wp_global_styles',
        'wp_navigation', 'acf-taxonomy', 'acf-post-type', 'acf-field-group',
        'acf-field', 'product'
    ];

    public function __construct($optionPrefix = 'page_for_')
    {
        $this->optionPrefix = $optionPrefix;
    }

    public function init() :void
    {
        // Actions
        add_action('init',                                  [$this, 'getCustomPostTypes'], 20);
        add_action('init',                                  [$this, 'onOptionUpdate'], 25);
        add_action('init',                                  [$this, 'redirect'], 30);
        add_action('init',                                  [$this, 'rewriteRules'], 35);
        add_action('admin_init',                            [$this, 'readingOptions'], 1);
        add_action('admin_head',                            [$this, 'removeEditor']);
        add_action('edit_form_after_title',                 [$this, 'addNotices']);
        add_action('updated_option',                        [$this, 'updateTranslationSlugs']);
        add_action('pre_get_posts',                         [$this, 'updatePostTypeQuery'], 1);
        add_action('admin_bar_menu',                        [$this, 'addArchivePageInAdminBar'], 100);

        // Filters
        add_filter('display_post_states',                   [$this, 'displayPageName']);
        add_filter('wp_nav_menu_objects',                   [$this, 'overrideMenuObjects'], 1, 2 );
        add_filter('wp_setup_nav_menu_item',                [$this, 'addCustomPageLabels'], 1, 2 );
        add_filter('register_post_type_args',               [$this, 'overrideCustomPostTypeSlug'], 10, 2 );
        add_filter('page_template_hierarchy',               [$this, 'transformPageAsArchive'], 5, 1);

        // Yoast SEO
        add_filter('wpseo_breadcrumb_links',                [$this, 'overrideYoastBreadcrumb'], 10, 1);
        add_filter('wp_title',                              [$this, 'overrideYoastArchiveTitle'], 20, 1);

        // ACF
        add_action('acf/init',                              [$this, 'getCustomPostTypes'], 10);
        add_filter('acf/post_type/registration_args',       [$this, 'overrideACFCustomPostTypeSlug'], 10, 2);
        add_filter('acf/location/rule_values/page_type',    [$this, 'addACFPageTypeValue']);
        add_filter('acf/location/rule_match/page_type',     [$this, 'addACFPageTypeMatch'], 10, 4);

        // WPML
        add_action('post_updated',                          [$this, 'autoRegisterLanguagesSlugs'], 10, 3);
    }

    /**
     * Check option value before update
     * @return void
     */
    public function onOptionUpdate()
    {
        foreach ($this->postTypes as $postType){
            if(is_a($postType, 'WP_Post_Type')) {
                $option = $this->optionPrefix . $postType->name;
                add_filter('pre_update_option_' . $option, [$this, 'alterPageOptionBeforeUpdate'], 10, 3);
            }
        }
    }

    /**
     * Redirect user if custom post type if archive accessed directly
     * @return void
     */
    public function redirect () {

        foreach ($this->postTypes as $postType){
            if(is_a($postType, 'WP_Post_Type')) {

                $page = get_post(get_option(($this->optionPrefix . $postType->name)));

                if ($page && $this->getCurrentLanguage()) {

                    $this->addMultilingualRedirects($page, $postType);

                } else if ($page) {

                    $permalink = get_post_type_archive_link($postType->name);
                    $currentURL = $this->getCurrentURL();
                    $pageLink = ($page) ? get_permalink($page->ID) : null;

                    if ($currentURL && ($pageLink && $permalink == $currentURL['url'] && $permalink != $pageLink)) {

                        if ($currentURL['query']) {
                            $pageLink = $pageLink . '?' . $currentURL['query'];
                        }

                        wp_redirect($pageLink, 301);

                    }

                }
            }
        }

    }

    /**
     * Get current URL
     * @return array
     */
    public function getCurrentURL()
    {
        $result = false;

        if(isset($_SERVER['HTTPS']) && isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])){
            $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $result = parse_url($url);

            $result = [
                'url'   => $result['scheme'].'://'.$result['host'].$result['path'],
                'query' => (isset($result['query'])) ? $result['query'] : '',
            ];
        }

        return $result;
    }

    /**
     * Add Custom post type rewrite rules to prevent if archive page is child of another
     * @return void
     */
    public function rewriteRules ()
    {
        global $wp_rewrite;

        foreach ($this->postTypes as $postType){

            if(is_a($postType, 'WP_Post_Type')) {
                $page = get_post(get_option($this->optionPrefix . $postType->name));

                if ($page && $this->getCurrentLanguage()) {

                    $this->addMultilingualRewriteRules($page, $postType);

                } else if ($page) {

                    $archive_slug = trim(str_replace(get_home_url(), '', get_permalink($page->ID)), '/');
                    add_rewrite_rule("{$archive_slug}/?$", "index.php?post_type=$postType->name", 'top');
                    if ($postType->rewrite['pages']) {
                        add_rewrite_rule("{$archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$", "index.php?post_type=$postType->name" . '&paged=$matches[1]', 'top');
                    }

                }
            }

        }
    }

    /**
     * Get registered Custom Post Types
     * @return void
     */
    public function getCustomPostTypes () :void
    {

        $postTypes = get_post_types([
            'public' => true,
            '_builtin' => false
        ]);

        foreach ($postTypes as $key => $postType){
            if(!in_array($key, $this->excludedPostTypes)){
                $postTypes[$key] = get_post_type_object($postType);
            }
        }

        $this->postTypes = $postTypes;

    }

    /**
     * Add reading option selectors in read settings
     * @return void
     */
    public function readingOptions() :void
    {
        if(is_array($this->postTypes) && count($this->postTypes)){

            $hasPublicPostType = false;

            foreach ($this->postTypes as $postType) {
                if ( is_a( $postType, 'WP_Post_Type' ) && $postType->publicly_queryable ) {
                    $hasPublicPostType = true;
                }
            }

            if($hasPublicPostType){
                add_settings_section( 'page_for_post_type', __( 'Réglages des types de contenu', 'coqpit-core' ), '__return_false', 'reading' );

                foreach ($this->postTypes as $postType){
                    if(is_a($postType, 'WP_Post_Type') && $postType->publicly_queryable) {
                        $option = $this->optionPrefix . $postType->name;
                        $name = sprintf(__('Page des %s', 'coqpit-core'), mb_strtolower($postType->labels->name));

                        register_setting('reading', $option, [
                            'type' => 'string',
                            'sanitize_callback' => 'sanitize_text_field',
                            'default' => NULL,
                        ]);

                        $optionID = $this->getOptionPageID($option);

                        add_settings_field($option, $name, function ($args) use ($option, $name, $optionID) {

                            echo wp_dropdown_pages([
                                'name' => $option,
                                'echo' => 0,
                                'show_option_none' => __('&mdash; Select &mdash;'),
                                'option_none_value' => '0',
                                'selected' => $optionID,
                            ]);

                        }, 'reading', 'page_for_post_type', ['label_for' => $option]);


                        $type = $postType->name;
                        $option = $type.'_per_page';
                        $name = __('Les pages du site doivent afficher au plus', 'coqpit-core');
                        $value = (get_option($option)) ?: get_option('posts_per_page');

                        register_setting('reading', $option, [
                            'type' => 'number',
                            'sanitize_callback' => 'sanitize_text_field',
                            'default' => NULL,
                        ]);

                        add_settings_field($option, $name, function ($args) use ($option, $name, $optionID, $postType, $value) {

                            echo '<input name="'.$option.'" type="number" step="1" min="1" id="'.$option.'" value="'.$value.'" class="small-text" /> '.$postType->labels->name;

                        }, 'reading', 'page_for_post_type', ['label_for' => $option]);
                    }
                }
            }
        }
    }

    /**
     * Remove editor from custom post type page in admin
     * @return void
     */
    public function removeEditor() :void
    {
        global $post;

        if(!empty($post)){
            foreach ($this->postTypes as $postType){
                if(is_a($postType, 'WP_Post_Type')) {
                    $option = $this->optionPrefix . $postType->name;
                    $pageID = $this->getOptionPageID($option);

                    if ($post && 'page' == get_post_type($post->ID) && $post->ID == $pageID && $pageID != '0') {
                        remove_post_type_support('page', 'editor');
                    }
                }
            }
        }
    }

    /**
     * Display notice on custom post types pages
     * @return void
     */
    public function addNotices() :void
    {
        global $post;

        if(!empty($post)) {
            foreach ($this->postTypes as $postType) {
                if(is_a($postType, 'WP_Post_Type')) {
                    $option = $this->optionPrefix . $postType->name;
                    $pageID = $this->getOptionPageID($option);

                    if ('page' == get_post_type($post->ID) && $post->ID == $pageID && $pageID != '0') {
                        $class = 'notice notice-warning inline';
                        $message = sprintf(__('Vous êtes en train de modifier la page qui affiche des %s.', 'coqpit-core'), mb_strtolower($postType->labels->name));

                        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
                    }
                }
            }
        }
    }

    /**
     * Add direct admin links in admin bar
     * @return void
     */
    public function addArchivePageInAdminBar ($adminBar)
    {

        foreach ($this->postTypes as $postType){
            if(is_a($postType, 'WP_Post_Type')) {
                $option = $this->optionPrefix . $postType->name;
                $page = $this->getOptionPageID($option);

                if (!is_admin() && is_post_type_archive($postType->name) && $page) {
                    $adminBar->add_node([
                        'id'        => 'edit',
                        'parent'    => false,
                        'title'     => __('Modifier la page', 'coqpit-core'),
                        'href'      => get_edit_post_link($page)
                    ]);

                    $editPostTypeLink = admin_url('edit.php?post_type=' . $postType->name);

                    if ($this->getCurrentLanguage()) {
                        $editPostTypeLink = admin_url('edit.php?post_type=' . $postType->name . '&lang=' . $this->getCurrentLanguage());
                    }

                    $adminBar->add_node([
                        'id'        => 'items',
                        'parent'    => false,
                        'title'     => __('Modifier les ' . mb_strtolower($postType->labels->name), 'coqpit-core'),
                        'href'      => $editPostTypeLink
                    ]);
                }

                if (is_admin()) {
                    $screen = get_current_screen();
                    if ($screen->id == 'edit-' . $postType->name && $page) {
                        $adminBar->add_node([
                            'id'        => 'archive',
                            'parent'    => false,
                            'title'     => $postType->labels->view_items,
                            'href'      => get_permalink($page)
                        ]);
                    }
                }
            }
        }

    }

    /**
     * Force custom post type linked page to use an archive template
     * @param $templates
     * @return string[]
     */
    public function transformPageAsArchive ($templates)
    {

        foreach($this->postTypes as $postType){
            if(is_a($postType, 'WP_Post_Type')) {
                $option = $this->optionPrefix . $postType->name;

                if (!is_admin() && is_page() && get_the_ID() == $this->getOptionPageID($option)) {
                    $templates = ['archive-' . $postType->name . '.php', 'archive.php'];
                    $templateManager = new Templates();
                    $defaultTemplates = array_reverse($templates);

                    foreach ($defaultTemplates as $template) {
                        array_unshift($templates, $templateManager->getHierarchyPath() . '/' . $postType->name . '/' . $template);
                    }
                }
            }
        }

        return $templates;

    }

    /**
     * Display page option name in list of pages
     * @return array
     */
    public function displayPageName($states) :array
    {
        global $post;

        if(empty($post)) return $states;

        foreach ($this->postTypes as $postType) {
            if(is_a($postType, 'WP_Post_Type')) {
                $option = $this->optionPrefix . $postType->name;
                $name = sprintf(__('Page des %s', 'coqpit-core'), mb_strtolower($postType->labels->name));
                $pageID = $this->getOptionPageID($option);

                if ($post && 'page' == get_post_type($post->ID) && $post->ID == $pageID && $pageID != '0') {
                    $states[$option] = $name;
                }
            }
        }

        return $states;
    }

    /**
     * Add menu item classes when archive page is used in navigations
     *
     * @param $sortedItems
     * @param $args
     * @return array
     */
    public function overrideMenuObjects($sortedItems, $args) :array
    {

        global $wp_query;

        $queriedObject = get_queried_object();

        if (!$queriedObject) {
            return $sortedItems;
        }

        $objectPostType = false;

        if (is_singular()) {
            $objectPostType = $queriedObject->post_type;
        }

        if ( is_post_type_archive()) {
            $objectPostType = $queriedObject->name;
        }

        if (is_archive() && is_string($wp_query->get('post_type'))) {
            $queryPostType  = $wp_query->get( 'post_type' );
            $objectPostType = $queryPostType ?: 'post';
        }

        if (!$objectPostType) {
            return $sortedItems;
        }

        $pages = [];

        foreach ($this->postTypes as $postType){
            if(is_a($postType, 'WP_Post_Type')) {
                $option = $this->optionPrefix . $postType->name;
                $page = $this->getOptionPageID($option);
                if ($page) $pages[$postType->name] = $page;
            }
        }

        if (!isset($pages[$objectPostType])) {
            return $sortedItems;
        }

        foreach ( $sortedItems as &$item ) {
            if ($item->type === 'post_type' && $item->object === 'page' && intval($item->object_id) === intval($pages[$objectPostType])) {
                if (is_singular($objectPostType)) {
                    $item->classes[]                = 'current-menu-item-ancestor';
                    $item->current_item_ancestor    = true;
                    $sortedItems                    = $this->addAncestorClass($item, $sortedItems);
                }
                if (is_post_type_archive($objectPostType)) {
                    $item->classes[]    = 'current-menu-item';
                    $item->current_item = true;
                    $sortedItems        = $this->addAncestorClass($item, $sortedItems);
                }
                if (is_archive() && $objectPostType === $wp_query->get( 'post_type')) {
                    $sortedItems = $this->addAncestorClass($item, $sortedItems);
                }
            }
        }

        return $sortedItems;

    }

    /**
     * Rename menu item box when using an archive page
     *
     * @param $menuItem
     * @return mixed
     */
    public function addCustomPageLabels ($menuItem)
    {
        foreach ($this->postTypes as $postType){
            if(is_a($postType, 'WP_Post_Type')) {
                $option = $this->optionPrefix . $postType->name;
                $page = $this->getOptionPageID($option);
                if ($page && $page == $menuItem->object_id) $menuItem->type_label = sprintf(__('Page des %s', 'coqpit-core'), mb_strtolower($postType->labels->name));
            }
        }

        return $menuItem;
    }

    /**
     * Override default slug with page slug
     * @param $args
     * @param $postType
     * @return array
     */
    public function overrideCustomPostTypeSlug ($args, $postType) :array
    {

        $option = $this->optionPrefix.$postType;
        $page = get_post(get_option($option));

        if($page){
            $args['rewrite']['slug'] = $page->post_name;
        }

        return $args;
    }

    /**
     * Add automatically custom page link to the Yoast SEO Breadcrumb
     * @param $links
     * @return array
     */
    public function overrideYoastBreadcrumb ($links) :array
    {

        $newLinks = [];

        foreach ($links as $link){
            if(isset($link['ptarchive'])){
                $option = $this->optionPrefix.$link['ptarchive'];
                $page = get_post($this->getOptionPageID($option));
                if($page){
                    $ancestors = get_post_ancestors($page);

                    if($ancestors){
                        foreach($ancestors as $ancestor){
                            $newLinks[] = [
                                'url'   => get_the_permalink($ancestor),
                                'text'  => get_the_title($ancestor)
                            ];
                        }
                    }

                    $newLinks[] = [
                        'url' => get_the_permalink($page),
                        'text' => get_the_title($page),
                        'ptarchive' => $link['ptarchive']
                    ];

                } else {
                    $newLinks[] = $link;
                }
            } else {
                $newLinks[] = $link;
            }
        }

        return $newLinks;

    }

    /**
     * Yoast Custom Post Type archive title override
     * @param $title
     * @return void
     */
    public function overrideYoastArchiveTitle ($title){

        foreach ($this->postTypes as $postType){
            if(is_a($postType, 'WP_Post_Type') && is_post_type_archive($postType->name) && function_exists('YoastSEO')) {
                $option = $this->optionPrefix.$postType->name;
                $page = get_post($this->getOptionPageID($option));
                $title = YoastSEO()->meta->for_post($page->ID)->title;
            }
        }

        return $title;
    }

    /**
     * Override default slug with page slug
     * @param $args
     * @param $post
     * @return array
     */
    public function overrideACFCustomPostTypeSlug ($args, $post) :array
    {

        $option = $this->optionPrefix.$post['post_type'];
        $page = get_post(get_option($option));

        if($page){
            $args['has_archive'] = $page->post_name;
            $args['rewrite']['slug'] = $page->post_name;
        }

        return $args;
    }

    /**
     * Add ACF Page type for Fields groups
     *
     * @param $choices
     * @return mixed
     */
    public function addACFPageTypeValue ($choices)
    {
        foreach ($this->postTypes as $postType){
            if(is_a($postType, 'WP_Post_Type')) {
                $option = $this->optionPrefix . $postType->name;
                $choices[$option] = sprintf(__('Page des %s', 'coqpit-core'), mb_strtolower($postType->labels->name));
            }
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

        foreach ($this->postTypes as $postType) {
            if(is_a($postType, 'WP_Post_Type')) {
                $option = $this->optionPrefix . $postType->name;
                if ($rule['value'] == $option) {
                    if ($rule['operator'] == "==") {
                        $match = ($post && $this->getOptionPageID($option) == $post->ID);
                    } elseif ($rule['operator'] == "!=") {
                        $match = ($post && $this->getOptionPageID($option) != $post->ID);
                    }
                }
            }
        }

        return $match;

    }

    /**
     * Specific function for menu items classes
     *
     * @param $child
     * @param $items
     * @return mixed
     */
    protected function addAncestorClass($child, $items) {

        if (!intval($child->menu_item_parent)) {
            return $items;
        }

        foreach ($items as $item) {
            if (intval($item->ID) === intval($child->menu_item_parent)) {
                $item->classes[]             = 'current-menu-item-ancestor';
                $item->current_item_ancestor = true;
                if (intval($item->menu_item_parent)) {
                    $items = $this->addAncestorClass($item, $items);
                }
                break;
            }
        }

        return $items;
    }

    /**
     * Add per_page variable for each custom post type queries
     *
     * @param $query
     * @return \WP_Query
     */
    public function updatePostTypeQuery(\WP_Query $query)
    {

        foreach ($this->postTypes as $postType) {
            if(is_object($postType) && $query->is_post_type_archive($postType->name)){
                if(!isset($query->query['posts_per_page'])){
                    $type = $postType->name;
                    $optionName = $type.'_per_page';
                    $value = (get_option($optionName)) ?: false;

                    if($value){
                        $query->set('posts_per_page', $value);
                    }
                }
            }
        }

        return $query;

    }

}