<?php

namespace COQPIT\Plugins\Core\WordPress;

class Access {

    protected $capabilities = [
        'add_administrator_users',
        'edit_medias_settings',
        'edit_emails_settings',
        'edit_writing_settings',
        'edit_general_settings',
        'manage_tools',
        'manage_acf',
        'edit_access_settings',
    ];

    public $lockedMenus = [
        'plugins.php',
        'edit.php?post_type=acf-field-group',
    ];
    public $lockedSubMenus = [
        'index.php|update-core.php',
        'options-general.php|options-general.php',
        'options-general.php|options-writing.php',
        'options-general.php|options-media.php',
        'options-general.php|emails',
        'options-general.php|access',
        'themes.php|themes.php',
        'themes.php|site-editor.php?path=/patterns',
        'plugins.php|plugins.php',
        'plugins.php|plugin-install.php',
        'tools.php|tools.php',
        'tools.php|site-health.php',
        'tools.php|import.php',
        'tools.php|export.php',
    ];

    public $hiddenMenus = [];
    public $hiddenSubMenus = [];

    public function __construct() {
        $this->lockedMenus = apply_filters('coqpit_access_locked_menus', $this->lockedMenus);
        $this->lockedSubMenus = apply_filters('coqpit_access_locked_submenus', $this->lockedSubMenus);
    }

    public function init()
    {
        $this->hiddenMenus = (get_option('coqpit_access_menus')) ?: [];
        $this->hiddenSubMenus = (get_option('coqpit_access_submenus')) ?: [];

        add_action('admin_init',                    [$this, 'initializeRole']);
        add_action('admin_init',                    [$this, 'checkCurrentScreen'], 999);
        add_action('admin_menu',                    [$this, 'customAccessRules'], 9999);
        add_action('admin_init',                    [$this, 'AccessOptions'], 9999);
        add_action('delete_user',                   [$this, 'preventAdminDeletion'], 10, 3);
        add_action('admin_notices',                 [$this, 'displayNoticeAfterRemoveAttempt']);
        add_action('admin_menu',                    [$this, 'optionPage']);
        add_action('before_access_settings_page',   [$this, 'navigationDisplay']);
        add_action('admin_bar_menu',                [$this, 'removeAccessFromAdminBar'], 9999);

        add_filter('editable_roles',                    [$this, 'rewriteRoleDropdown']);
        add_filter('user_row_actions',                  [$this, 'rewriteUserTableActions'], 10, 2 );
        add_filter('admin_body_class',                  [$this, 'addRoleClassToBody']);
        add_filter('pre_update_option_wp_user_roles',   [$this, 'autoUpdateCapabilities'], 10);

        register_activation_hook( COQPIT_CORE_PLUGIN_FILE, [self::class, 'register']);
        register_deactivation_hook( COQPIT_CORE_PLUGIN_FILE, [self::class, 'unregister']);
    }

    public function optionPage()
    {
        if(current_user_can('edit_access_settings')) {
            add_options_page(
                __('Accès', 'coqpit-core'),
                __('Accès', 'coqpit-core'),
                'manage_options',
                'access',
                [$this, 'optionPageInner']
            );

        }
    }

    public function optionPageInner()
    {
        include_once COQPIT_CORE_PATH.'admin/access-settings.php';
    }

    public function navigationDisplay()
    {
        include_once COQPIT_CORE_PATH.'admin/users-navigation-display.php';
    }

    public function AccessOptions()
    {
        if(current_user_can('edit_access_settings')) {

            register_setting('access', 'coqpit_access_menus', [
                'type' => 'array',
                'default' => [],
            ]);

            register_setting('access', 'coqpit_access_submenus', [
                'type' => 'array',
                'default' => [],
            ]);

        }
    }

    public function rewriteRoleDropdown($roles){
        $screen = get_current_screen();

        if(($screen->id == 'user' || $screen->id == 'users') && !current_user_can('add_administrator_users')) {
            unset($roles['administrator']);
        }

        return $roles;
    }

    public function rewriteUserTableActions($actions, $user)
    {
        if($user->has_cap('administrator') && !current_user_can('add_administrator_users')) {
            unset($actions['edit']);
            unset($actions['delete']);
            unset($actions['resetpassword']);
        }

        return $actions;
    }

    public function initializeRole()
    {
        $roles = $this->getInstance();
        $customer = $roles->get_role('coqpit_admin');
        if(!$customer) {
            $admin = $roles->get_role('administrator');
            foreach ($this->capabilities as $capability) {
                $admin->add_cap($capability);
            }
            $roles->add_role('coqpit_admin', __('Gestionnaire', 'coqpit-core'), $this->rewriteAdminCapabilities($admin->capabilities));
        }
    }

    public static function register()
    {
        $self = new self();
        $roles = $self->getInstance();
        $customer = $roles->get_role('coqpit_admin');

        if($customer){
            $roles->remove_role('coqpit_admin');
        }

        if(!$customer) {
            $admin = $roles->get_role('administrator');
            foreach ($self->capabilities as $capability) {
                $admin->add_cap($capability);
            }
            $roles->add_role('coqpit_admin', __('Gestionnaire', 'coqpit-core'), $self->rewriteAdminCapabilities($admin->capabilities));
        }
    }

    public static function unregister()
    {
        $self = new self();
        $roles = $self->getInstance();

        // Remove custom capabilities for administrators
        $admin = $roles->get_role('administrator');

        foreach ($self->capabilities as $capability) {
            $admin->remove_cap($capability);
        }

        // Remove custom role
        $roles->remove_role('coqpit_admin');
    }

    public function autoUpdateCapabilities($value)
    {

        if(isset($value['coqpit_admin'])){
            $value['coqpit_admin']['capabilities'] = $this->rewriteAdminCapabilities($value['administrator']['capabilities']);
        }

        return $value;
    }

    public function getInstance()
    {
        global $wp_roles;

        if (!isset($wp_roles)) $wp_roles = new \WP_Roles();

        return $wp_roles;
    }

    public function rewriteAdminCapabilities(array $capabilities): array
    {
        // WordPress's Core
        unset($capabilities['update_core']);
        unset($capabilities['edit_files']);

        // COQPIT Core
        foreach ($this->capabilities as $capability) {
            unset($capabilities[$capability]);
        }

        // Themes
        unset($capabilities['switch_themes']);
        unset($capabilities['edit_themes']);
        unset($capabilities['install_themes']);
        unset($capabilities['delete_themes']);
        unset($capabilities['update_themes']);

        // Plugins
        unset($capabilities['edit_plugins']);
        unset($capabilities['activate_plugins']);
        unset($capabilities['update_plugins']);
        unset($capabilities['delete_plugins']);
        unset($capabilities['install_plugins']);

        return $capabilities;
    }

    public function checkCurrentScreen()
    {
        add_action('current_screen', [$this, 'customAccessRedirects']);
    }

    public function customAccessRedirects()
    {
        if(is_admin() && current_user_can('manage_options')) {
            $screen = get_current_screen();

            $redirectRules = [
                'tools'                         => 'manage_tools',
                'import'                        => 'manage_tools',
                'export'                        => 'manage_tools',
                'options-general'               => 'edit_general_settings',
                'options-writing'               => 'edit_writing_settings',
                'options-media'                 => 'edit_medias_settings',
                'settings_page_emails'          => 'edit_emails_settings',
                'settings_page_access'          => 'edit_access_settings',
                'edit-acf-field-group'          => 'manage_acf',
                'edit-acf-post-type'            => 'manage_acf',
                'edit-acf-taxonomy'             => 'manage_acf',
                'edit-acf-ui-options-page'      => 'manage_acf',
                'acf_page_acf-tools'            => 'manage_acf',
                'acf_page_acf-settings-updates' => 'manage_acf',
            ];

            foreach ($redirectRules as $screenID => $capability) {
                if($screen->id == $screenID && !current_user_can($capability)) {
                    wp_redirect(admin_url());
                    exit;
                }
            }
        }

        if(is_admin() && !current_user_can('edit_access_settings')){
            $currentSlug = urldecode(basename($_SERVER['REQUEST_URI']));
            $menus = array_unique([...$this->lockedMenus, ...$this->hiddenMenus]);
            $subMenus = array_unique([...$this->lockedSubMenus, ...$this->hiddenSubMenus]);

            if(in_array($currentSlug, $menus)) {
                wp_redirect(admin_url());
                exit;
            }

            foreach ($subMenus as $subMenu){
                $subMenu = explode('|', $subMenu);
                if($currentSlug == $subMenu[1]) {
                    wp_redirect(admin_url());
                    exit;
                }
            }
        }
    }

    public function customAccessRules()
    {

        if(!current_user_can('edit_general_settings')) {
            remove_submenu_page('options-general.php', 'options-general.php');
        }

        if(!current_user_can('edit_writing_settings')) {
            remove_submenu_page('options-general.php', 'options-writing.php');
        }

        if(!current_user_can('manage_tools')) {
            remove_submenu_page('tools.php', 'tools.php');
            remove_submenu_page('tools.php', 'site-health.php');
            remove_submenu_page('tools.php', 'import.php');
            remove_submenu_page('tools.php', 'export.php');
        }

        if(!current_user_can('manage_acf')) {
            remove_menu_page('edit.php?post_type=acf-field-group');
        }

        if(!current_user_can('edit_access_settings')) {

            $menus = array_unique([...$this->lockedMenus, ...$this->hiddenMenus]);
            $subMenus = array_unique([...$this->lockedSubMenus, ...$this->hiddenSubMenus]);


            //dd($menus, $subMenus);
            foreach($menus as $menu) {
                remove_menu_page($menu);
            }

            foreach($subMenus as $subMenu) {
                $subMenu = explode('|', $subMenu);
                remove_submenu_page($subMenu[0], $subMenu[1]);
            }
        }

    }

    public function addRoleClassToBody($classes)
    {
        if(!current_user_can('add_administrator_users')) {
            $classes .= ' cant-delete-user';
        }

        return $classes;
    }

    public function preventAdminDeletion($userID, $reassign, $user)
    {
        if($user->has_cap('administrator') && !current_user_can('add_administrator_users')) {
            wp_redirect('/wp-admin/users.php?error=remove_admin');
            exit;
        }
    }

    public function displayNoticeAfterRemoveAttempt()
    {
        if(isset($_GET['error']) && $_GET['error'] == 'remove_admin') {
            printf( '<div class="notice notice-error"><p>%1$s</p></div>', esc_attr( __( 'Vous ne disposez pas des droits pour supprimer un administrateur.', 'coqpit-core' ) ) );
        }
    }

    public static function getAdminNavigations(): array
    {
        global $menu;
        global $submenu;

        $self = new self();

        $hiddenMenus = (get_option('coqpit_access_menus')) ?: [];
        $hiddenSubMenus = (get_option('coqpit_access_submenus')) ?: [];

        $navigations = [];

        foreach ($menu as $parent) {
            if(!empty($parent[0]) && !empty($parent[2])){

                $args = [
                    'name'      => trim(preg_replace('/<[^>]*>(.*)?<\/[^>]*>/m', '', $parent[0])),
                    'display'   => true,
                    'locked'    => false,
                    'children'  => []
                ];

                if(in_array($parent[2], $hiddenMenus)){
                    $args['display'] = false;
                }

                if(in_array($parent[2], $self->lockedMenus)){
                    $args['display'] = false;
                    $args['locked'] = true;
                }

                $navigations[$parent[2]] = $args;
            }
        }

        foreach ($submenu as $slug => $children) {
            if(isset($navigations[$slug])){
                foreach ($children as $child) {
                    if(!empty($child[0]) && !empty($child[2])){

                        $args = [
                            'name'      => trim(preg_replace('/<[^>]*>(.*)?<\/[^>]*>/m', '', $child[0])),
                            'display'   => true,
                            'locked'    => false,
                        ];

                        if(in_array($slug.'|'.$child[2], $hiddenSubMenus)){
                            $args['display'] = false;
                        }

                        if(in_array($slug.'|'.$child[2], $self->lockedSubMenus)){
                            $args['display'] = false;
                            $args['locked'] = true;
                        }

                        $navigations[$slug]['children'][$slug.'|'.$child[2]] = $args;
                    }
                }
            }
        }

        return $navigations;
    }

    public function removeAccessFromAdminBar($adminBar)
    {

        if(!current_user_can('add_administrator_users')) {

            $postTypesLocked = $this->getLockedPosTypes();

            foreach ($postTypesLocked as $postType) {
                if(get_post_type() == $postType && !is_admin() && !is_post_type_archive()) {
                    $adminBar->remove_node('edit');
                }
            }

            if(is_post_type_archive() && in_array('page', $postTypesLocked) && !is_admin()){
                $adminBar->remove_node('edit');
            }

            foreach($this->hiddenMenus as $menu){
                foreach($adminBar->get_nodes() as $node){
                    if($node->href == admin_url($menu) || $node->href == admin_url('admin.php?page='.$menu)){
                        $adminBar->remove_node($node->id);
                    }
                }
            }

            foreach($this->hiddenSubMenus as $menu){
                $subMenu = explode('|', $menu);
                foreach($adminBar->get_nodes() as $node){
                    if($node->href == admin_url($subMenu[1]) || $node->href == admin_url('admin.php?page='.$subMenu[1])){
                        $adminBar->remove_node($node->id);
                    }
                }
            }
        }
    }

    public function getLockedPosTypes()
    {

        $postTypesLocked = [];

        foreach($this->hiddenMenus as $menu){
            $query = parse_url($menu, PHP_URL_QUERY);
            parse_str($query, $params);
            if(isset($params['post_type'])) $postTypesLocked[] = $params['post_type'];
        }

        foreach($this->hiddenSubMenus as $menu){
            $subMenu = explode('|', $menu);
            $queryParent = parse_url($subMenu[0], PHP_URL_QUERY);
            parse_str($queryParent, $paramsParent);
            if(isset($paramsParent['post_type'])) $postTypesLocked[] = $paramsParent['post_type'];
            $queryChild = parse_url($subMenu[0], PHP_URL_QUERY);
            parse_str($queryChild, $paramsChild);
            if(isset($paramsChild['post_type'])) $postTypesLocked[] = $paramsChild['post_type'];
        }

        return array_unique($postTypesLocked);

    }

}
