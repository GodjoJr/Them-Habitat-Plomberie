<?php

namespace COQPIT\Plugins\Core\Template;

use COQPIT\Plugins\Core\Traits\HasWPML;

class Gutenberg {

    use HasWPML;

    public static function disable() :void
    {
        add_filter('use_block_editor_for_post', function ($currentStatus, $post){

            return false;

        }, 20, 2);

        add_action( 'wp_enqueue_scripts', function (){
            wp_dequeue_style( 'wp-block-library' );
            wp_dequeue_style( 'wp-block-library-theme' );
            wp_dequeue_style( 'wc-block-style' );
            wp_dequeue_style( 'storefront-gutenberg-blocks' );
        }, 100 );

    }

    public static function disableForHomePage() :void
    {
        add_filter('use_block_editor_for_post', function ($currentStatus, $post){

            $pageID = get_option('page_on_front');

            if('page' == $post->post_type && $post->ID == $pageID && $pageID != '0'){
                return false;
            }

            return $currentStatus;

        }, 20, 2);
    }

    public static function disableForPostsPage() :void
    {
        add_filter('use_block_editor_for_post', function ($currentStatus, $post){

            $pageID = get_option('page_for_posts');

            if('page' == $post->post_type && $post->ID == $pageID && $pageID != '0'){
                return false;
            }

            return $currentStatus;

        }, 20, 2);
    }

    public static function disableForPostID($id) :void
    {
        add_filter('use_block_editor_for_post', function ($currentStatus, $post) use ($id){

            if($post->ID == $id && $id != '0'){
                return false;
            }

            return $currentStatus;

        }, 20, 2);
    }

    public static function disableForPostType($post_type) :void
    {
        add_filter('use_block_editor_for_post', function ($currentStatus, $post) use ($post_type){

            if($post_type == $post->post_type){
                return false;
            }

            return $currentStatus;

        }, 20, 2);
    }

    public static function disableForStaticPage($option_page) :void
    {
        $self = (new self);
        add_filter('use_block_editor_for_post', function ($currentStatus, $post) use ($option_page, $self){

            if($self->getOptionPageID($option_page) == $post->ID){
                return false;
            }

            return $currentStatus;

        }, 20, 2);
    }

    public static function disableForWidgets() :void
    {
        remove_theme_support( 'widgets-block-editor' );

        add_filter( 'gutenberg_use_widgets_block_editor', '__return_false', 100 );
        add_filter( 'use_widgets_block_editor', '__return_false' );
    }
}
