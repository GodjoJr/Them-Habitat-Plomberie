<?php

namespace COQPIT\Plugins\Core\CustomFields;

class Synchronization {

    public string $path;
    public string $postType = 'acf-field-group';

    public function __construct($path = 'acf')
    {
        $this->path = get_template_directory().'/'.$path;
    }

    public function init() :void
    {
        if (!file_exists($this->path)){
            wp_mkdir_p($this->path);
        }

        $this->setup();
    }

    public function setup() :void
    {
        if(is_admin()){
            add_filter('acf/settings/save_json',    [$this, 'savePoint']);
            add_filter('acf/settings/load_json',    [$this, 'loadPoint']);
        }

        add_action('admin_bar_menu', [$this, 'displayPendingSync'], 400);
    }

    public function savePoint() :string
    {
        return $this->path;
    }

    public function loadPoint() :array
    {
        return [$this->path];
    }

    public function availableSynchronizations() :array
    {
        return [
            'acf-field-group'       => $this->hasFieldsSync(),
            'acf-post-type'         => $this->hasPostTypesSync(),
            'acf-taxonomy'          => $this->hasTaxonomiesSync(),
            'acf-ui-options-page'   => $this->hasOptionPagesSync()
        ];
    }

    public function hasFieldsSync() :bool
    {
        return !!($this->setupSync('acf-field-group'));
    }

    public function hasPostTypesSync() :bool
    {
        return !!(count($this->setupSync('acf-post-type')));
    }

    public function hasTaxonomiesSync() :bool
    {
        return !!(count($this->setupSync('acf-taxonomy')));
    }

    public function hasOptionPagesSync() :bool
    {
        return !!(count($this->setupSync('acf-ui-options-page')));
    }

    public function setupSync($postType) :array
    {
        $filesToSync = [];

        if ( acf_get_local_json_files( $postType ) ) {

            // Get all posts in a single cached query to check if sync is available.
            $all_posts = acf_get_internal_post_type_posts( $postType );
            foreach ( $all_posts as $post ) {

                // Extract vars.
                $local    = acf_maybe_get( $post, 'local' );
                $modified = acf_maybe_get( $post, 'modified' );
                $private  = acf_maybe_get( $post, 'private' );

                // Ignore if is private.
                if ( $private ) {
                    continue;

                    // Ignore not local "json".
                } elseif ( $local !== 'json' ) {
                    continue;

                    // Append to sync if not yet in database.
                } elseif ( ! $post['ID'] ) {
                    $filesToSync[ $post['key'] ] = $post;

                    // Append to sync if "json" modified time is newer than database.
                } elseif ( $modified && $modified > get_post_modified_time( 'U', true, $post['ID'] ) ) {
                    $filesToSync[ $post['key'] ] = $post;
                }
            }
        }

        return $filesToSync;
    }

    public function displayPendingSync(\WP_Admin_Bar $adminBar) :void
    {
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return;
        }

        $submenus = [];

        foreach ($this->availableSynchronizations() as $key => $sync){
            if($sync){

                switch($key){
                    case 'acf-post-type':
                        $name = __('Types de publication', 'coqpit-core');
                        break;
                    case 'acf-taxonomy':
                        $name = __('Taxonomies', 'coqpit-core');
                        break;
                    case 'acf-ui-options-page':
                        $name = __('Pages d\'options', 'coqpit-core');
                        break;
                    default:
                        $name = __('Groupes de champs', 'coqpit-core');
                        break;
                }

                $submenus[] = [
                    'id'        => 'acf-sync-'.$key,
                    'parent'    => 'acf-synchronization',
                    'href'      => 'edit.php?post_type='.$key.'&post_status=sync',
                    'title'     => $name
                ];

                $this->postType = $key;
            }
        }

        if(count($submenus) && current_user_can('manage_acf')){
            $adminBar->add_menu([
                'id'        => 'acf-synchronization',
                'parent'    => null,
                'href'      => 'edit.php?post_type='.$this->postType.'&post_status=sync',
                'title'     => _n('Synchronisation ACF en attente', 'Synchronisations ACF en attente', count($submenus), 'coqpit-core'),
            ]);

            foreach ($submenus as $submenu){
                $adminBar->add_menu($submenu);
            }
        }
    }

}
