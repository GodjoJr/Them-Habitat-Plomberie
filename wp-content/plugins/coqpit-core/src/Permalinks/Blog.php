<?php

namespace COQPIT\Plugins\Core\Permalinks;

use COQPIT\Plugins\Core\Traits\HasWPML;

class Blog {

    use HasWPML;

    /**
     * Add blog page's slug to the posts, categories and tags
     * @return void
     */
    public function init() :void
    {
        add_action('init',                      [$this, 'addRewriteRules'],                 10, 0);
        add_filter('post_link',                 [$this, 'updatePermalink'],                 10, 1);
        add_filter('category_link',             [$this, 'updatePermalink'],                 10, 1);
        add_filter('tag_link',                  [$this, 'updatePermalink'],                 10, 1);
        add_filter('wpseo_breadcrumb_links',    [$this, 'addBlogPageOnCategoriesAndTags'],  10, 1);
    }

    /**
     * Add rewrite rules for single posts, add blog page's slug before post's slug
     * @return void
     */
    public function addRewriteRules ()
    {
        if(!$this->getCurrentLanguage()){
            $blogPage = get_option('page_for_posts');
            if($blogPage){
                $blogPage = get_post($blogPage);
                if($blogPage){
                    add_rewrite_rule('^'.$blogPage->post_name.'/([^/]+)/?$', 'index.php?name=$matches[1]', 'top');

                    $categoryBase = get_option('category_base');
                    if($categoryBase){
                        add_rewrite_rule('^'.$blogPage->post_name.'/'.$categoryBase.'/([^/]+)/?$', 'index.php?category_name=$matches[1]', 'top');
                        add_rewrite_rule('^'.$blogPage->post_name.'/'.$categoryBase.'/([^/]+)/page/([^/]+)/?$', 'index.php?category_name=$matches[1]&paged=$matches[2]', 'top');
                    }

                    $tagBase = get_option('tag_base');
                    if($tagBase){
                        add_rewrite_rule('^'.$blogPage->post_name.'/'.$tagBase.'/([^/]+)/?$', 'index.php?tag_name=$matches[1]', 'top');
                        add_rewrite_rule('^'.$blogPage->post_name.'/'.$tagBase.'/([^/]+)/page/([^/]+)/?$', 'index.php?tag_name=$matches[1]&paged=$matches[2]', 'top');
                    }
                }
            }
        }
    }

    /**
     * Update posts, categories and tags links when url retrieved
     * @param $permalink
     * @return mixed|string
     */
    public function updatePermalink ($permalink)
    {

        $blogPage = $this->getOptionPageID('page_for_posts');
        if($blogPage){
            $blogPage = get_post($blogPage);
            if($blogPage){
                //$permalink = str_replace(rtrim(get_home_url(), '/'), rtrim(get_the_permalink($blogPage), '/'), $permalink);
            }
        }

        return $permalink;

    }

    /**
     * Add Yoast SEO archive page for blog categories and tags
     * @param $links
     * @return mixed
     */
    public function addBlogPageOnCategoriesAndTags ($links)
    {

        if(is_category()){
            $blogPage = get_option('page_for_posts');
            $last = array_pop($links);
            $links[] = [
                'url'   => get_permalink($blogPage),
                'text'  => get_the_title($blogPage),
                'id'    => $blogPage
            ];
            $links[] = $last;
        }

        if(is_tag()){
            $blogPage = get_option('page_for_posts');
            $last = array_pop($links);
            $links[] = [
                'url'   => get_permalink($blogPage),
                'text'  => get_the_title($blogPage),
                'id'    => $blogPage
            ];
            $links[] = $last;
        }

        return $links;
    }

}
