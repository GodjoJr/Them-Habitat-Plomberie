<?php

namespace COQPIT\Plugins\Core\Template;


class Templates {

    private string $hierarchyPath;

    public function __construct($path = 'templates')
    {
        $this->hierarchyPath = $path;
    }

    public function getHierarchyPath()
    {
        return $this->hierarchyPath;
    }

    /**
     * Add custom path to default template paths
     * @return void
     */
    public function init() :void
    {
        add_filter('404_template_hierarchy',            [$this, 'moveErrorTemplate'],       10, 1);
        add_filter('archive_template_hierarchy',        [$this, 'moveArchiveTemplate'],     10, 1);
        add_filter('author_template_hierarchy',         [$this, 'moveAuthorTemplate'],      10, 1);
        add_filter('category_template_hierarchy',       [$this, 'moveCategoryTemplate'],    10, 1);
        add_filter('date_template_hierarchy',           [$this, 'moveDateTemplate'],        10, 1);
        add_filter('frontpage_template_hierarchy',      [$this, 'moveFrontPageTemplate'],   10, 1);
        add_filter('home_template_hierarchy',           [$this, 'moveHomeTemplate'],        10, 1);
        add_filter('index_template_hierarchy',          [$this, 'moveIndexTemplate'],       10, 1);
        add_filter('page_template_hierarchy',           [$this, 'movePageTemplate'],        10, 1);
        add_filter('privacypolicy_template_hierarchy',  [$this, 'movePrivacyTemplate'],     10, 1);
        add_filter('search_template_hierarchy',         [$this, 'moveSearchTemplate'],      10, 1);
        add_filter('single_template_hierarchy',         [$this, 'moveSingleTemplate'],      10, 1);
        add_filter('singular_template_hierarchy',       [$this, 'moveSingularTemplate'],    10, 1);
        add_filter('tag_template_hierarchy',            [$this, 'moveTagTemplate'],         10, 1);
        add_filter('taxonomy_template_hierarchy',       [$this, 'moveTaxonomyTemplate'],    10, 1);
    }

    /**
     * Move error templates to "{theme}/templates/errors" folder
     * Example: {theme}/templates/errors/404.php
     * @param $templates
     * @return mixed
     */
    public function moveErrorTemplate ($templates)
    {

        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/errors/'.$template);
        }

        return $templates;
    }

    /**
     * Move custom post type archive templates to "{theme}/templates/{custom_post_type}" folder
     * Example: {theme}/templates/event/archive.php (for "event" custom post type)
     * @param $templates
     * @return mixed
     */
    public function moveArchiveTemplate ($templates)
    {
        if(get_queried_object()){
            $customPostType = get_queried_object()->name;
            $defaultTemplates = array_reverse($templates);

            foreach ($defaultTemplates as $template){
                array_unshift($templates, $this->hierarchyPath.'/'.$customPostType.'/'.$template);
            }
        }

        return $templates;
    }

    /**
     * Move author templates to "{theme}/templates/author" folder
     * Example: {theme}/templates/author/author.php
     * @param $templates
     * @return mixed
     */
    public function moveAuthorTemplate ($templates)
    {
        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/author/'.$template);
        }

        return $templates;
    }

    /**
     * Move date archive templates to "{theme}/templates/date.php" file
     * Example: {theme}/templates/post/category.php
     * @param $templates
     * @return mixed
     */
    public function moveDateTemplate ($templates)
    {
        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/'.$template);
        }

        return $templates;
    }

    /**
     * Move post category templates to "{theme}/templates/post" folder
     * Example: {theme}/templates/post/category.php
     * @param $templates
     * @return mixed
     */
    public function moveCategoryTemplate ($templates)
    {
        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/post/'.$template);
        }

        return $templates;
    }

    /**
     * Move home page template to "{theme}/templates/homepage.php" file
     * @param $templates
     * @return mixed
     */
    public function moveFrontPageTemplate ($templates)
    {
        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/'.$template);
        }

        array_unshift($templates, $this->hierarchyPath.'/homepage.php');

        return $templates;
    }

    /**
     * Move blog page template to "{theme}/templates/post/archive.php" file
     * @param $templates
     * @return mixed
     */
    public function moveHomeTemplate ($templates)
    {
        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/'.$template);
        }

        array_unshift($templates, $this->hierarchyPath.'/post/archive.php');

        return $templates;
    }

    /**
     * Move index default page template to "{theme}/templates/index.php" file
     * @param $templates
     * @return mixed
     */
    public function moveIndexTemplate ($templates)
    {
        array_unshift($templates, $this->hierarchyPath.'/index.php');
        return $templates;
    }

    /**
     * Move page templates to "{theme}/templates/page" folder
     * Example: {theme}/templates/page/page.php
     * @param $templates
     * @return mixed
     */
    public function movePageTemplate ($templates)
    {
        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/page/'.$template);
        }

        return $templates;
    }

    /**
     * Move privacy policy template to "{theme}/templates" folder
     * Example: {theme}/templates/privacy-policy.php
     * @param $templates
     * @return mixed
     */
    public function movePrivacyTemplate ($templates)
    {
        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/'.$template);
        }

        return $templates;
    }

    /**
     * Move search page template to "{theme}/templates/search.php" file
     * @param $templates
     * @return mixed
     */
    public function moveSearchTemplate ($templates)
    {
        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/'.$template);
        }

        return $templates;
    }

    /**
     * Move custom post type single templates to "{theme}/templates/{custom_post_type}" folder
     * Example: {theme}/templates/event/single.php (for "event" custom post type)
     * @param $templates
     * @return mixed
     */
    public function moveSingleTemplate ($templates)
    {
        global $wp_query;
        $customPostType = get_post_type($wp_query->get_queried_object());

        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/'.$customPostType.'/'.$template);
        }

        return $templates;
    }

    /**
     * Move singular default template to "{theme}/templates/singular.php" file
     * @param $templates
     * @return mixed
     */
    public function moveSingularTemplate ($templates)
    {
        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            array_unshift($templates, $this->hierarchyPath.'/'.$template);
        }

        return $templates;
    }

    /**
     * Move tags templates to "{theme}/templates/{post_type}" folder
     * Example: {theme}/templates/post/tag.php (for "post" post type)
     * @param $templates
     * @return mixed
     */
    public function moveTagTemplate ($templates)
    {
        $term = get_queried_object();
        $taxonomy = get_taxonomy($term->taxonomy);

        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            foreach ($taxonomy->object_type as $postType){
                array_unshift($templates, $this->hierarchyPath.'/'.$postType.'/'.$template);
            }
        }

        return $templates;
    }

    /**
     * Move taxonomy templates to "{theme}/templates/{post_type}" folder
     * Example: {theme}/templates/event/taxonomy.php (for "event" post type)
     * @param $templates
     * @return mixed
     */
    public function moveTaxonomyTemplate ($templates)
    {
        $term = get_queried_object();
        $taxonomy = get_taxonomy($term->taxonomy);
        $templates[] = 'archive.php';

        $defaultTemplates = array_reverse($templates);

        foreach ($defaultTemplates as $template){
            foreach ($taxonomy->object_type as $postType){
                array_unshift($templates, $this->hierarchyPath.'/'.$postType.'/'.$template);
            }
        }

        return $templates;
    }

}
