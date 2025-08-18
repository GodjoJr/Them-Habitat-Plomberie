<?php

namespace COQPIT\Plugins\Core\WordPress;

class Comments {

    public function init()
    {
        add_action('admin_init',                [$this, 'commentsOptions']);
        add_action('init',                      [$this, 'check']);
        add_filter('admin_body_class',          [$this, 'addCommentsDisabledClass']);
        add_action('admin_bar_menu',            [$this, 'removeAccessFromAdminBar'], 9999);
    }

    public function removeAccessFromAdminBar($adminBar)
    {
        if(get_option('coqpit_use_comments') !== 'on') {
            $adminBar->remove_node('comments');
        }
    }

    public function addCommentsDisabledClass($classes)
    {

        if(get_option('coqpit_use_comments') !== 'on'){
            $classes .= ' comments-disabled';
        }

        return $classes;
    }

    public function commentsOptions()
    {
        add_settings_section( 'coqpit-comments-management', __( 'Paramètres avancés', 'coqpit-core' ), '__return_false', 'discussion' );

        register_setting('discussion', 'coqpit_use_comments', [
            'type'              => 'boolean',
            'default'           => false,
        ]);

        add_settings_field('coqpit-comments-enabled-field', __('Activer/Désactiver les commentaires', 'coqpit-core'), [$this, 'renderCommentsSetting'], 'discussion', 'coqpit-comments-management', [
            'label_for' => 'coqpit_use_comments'
        ]);
    }

    public function renderCommentsSetting ()
    {
        $enabled = get_option( 'coqpit_use_comments' );
        $checked = ($enabled) ? ' checked="checked"' : '';
        echo '<label>';
        echo '<input id="coqpit-comments-enabled-field" name="coqpit_use_comments" type="checkbox"'.$checked.'/>';
        echo '<p style="max-width: 400px">'.__('Active ou désactive la fonctionnalité "Commentaires" au niveau du CMS', 'coqpit-core').'</p>';
        echo '</label>';
    }

    public function disableComments()
    {
        // Redirect any user trying to access comments page
        global $pagenow;

        if ($pagenow === 'edit-comments.php') {
            wp_safe_redirect(admin_url());
            exit;
        }

        // Remove comments metabox from dashboard
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

        // Disable support for comments and trackbacks in post types
        foreach (get_post_types() as $post_type) {
            if (post_type_supports($post_type, 'comments')) {
                remove_post_type_support($post_type, 'comments');
                remove_post_type_support($post_type, 'trackbacks');
            }
        }
    }

    /**
     * Check if website use comments based on options
     * @return void
     */
    public function check() {

        $useComments = get_option('coqpit_use_comments');

        if(!$useComments) {
            add_action('admin_init',        [$this, 'disableComments']);
            // Close comments on the front-end
            add_filter('comments_open',     '__return_false', 20, 2);
            add_filter('pings_open',        '__return_false', 20, 2);

            // Hide existing comments
            add_filter('comments_array',    '__return_empty_array', 10, 2);

            // Remove comments page in menu
            add_action('admin_menu', function () {
                remove_menu_page('edit-comments.php');
            });

            if (is_admin_bar_showing()) {
                remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
            }
        }

    }

}
