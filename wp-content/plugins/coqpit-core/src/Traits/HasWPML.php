<?php

namespace COQPIT\Plugins\Core\Traits;

trait HasWPML {

    /**
     * Return WPML Default language code
     * @return mixed|null
     */
    public function getDefaultLanguage()
    {
        return apply_filters( 'wpml_default_language', null );
    }

    /**
     * Return Current language or null if WPML is not active
     * @return mixed|null
     */
    public function getCurrentLanguage()
    {
        return apply_filters( 'wpml_current_language', null );
    }

    /**
     * Return WPML activated languages
     * @return array
     */
    public function getActiveLanguages()
    {
        return apply_filters( 'wpml_active_languages', []);
    }

    /**
     * Get translations of a post
     * @param $id
     * @return mixed|null
     */
    public function getTranslations($id)
    {

        $type = apply_filters('wpml_element_type', get_post_type( $id ) );
        $trid = apply_filters('wpml_element_trid', false, $id, $type );

        $translations = apply_filters( 'wpml_get_element_translations', [], $trid, $type );

        return $translations;

    }

    /**
     * Get page id based on option name and return translated page id if another language (work only with WPML)
     * @param $option
     * @return false|mixed|null
     */
    public function getOptionPageID ($option)
    {
        $pageID = get_option($option);

        if($this->getCurrentLanguage() && $pageID){
            $language = $this->getCurrentLanguage();
            $translations = $this->getTranslations($pageID);
            if(isset($translations[$language])){
                $pageID = $translations[$language]->element_id;
            }
        }

        return $pageID;
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

    /**
     * Update all WPML slugs on option update
     * @param $option
     * @param $value
     * @return void
     */
    public function updateTranslationSlugs ()
    {
        global $wpdb;

        if($this->getCurrentLanguage()){
            foreach ($this->postTypes as $postType){

                $post_type = (is_a($postType, \WP_Post_Type::class)) ? $postType->name : $postType;

                $option = $this->optionPrefix.$post_type;
                $pageID = $this->getOptionPageID($option);

                $originalTranslation = null;
                foreach ($this->getTranslations($pageID) as $translation){
                    if(!$translation->source_language_code){
                        $originalTranslation = $translation;
                    }
                }

                if($originalTranslation){
                    $originalPage = get_post($originalTranslation->element_id);
                    if($originalPage){
                        $postTypeName = (is_a($postType, \WP_Post_Type::class)) ? $postType->name : $postType;
                        $mainString = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}icl_strings WHERE language = '$originalTranslation->language_code' AND name = 'URL slug: $postTypeName'");

                        if($mainString){
                            $wpdb->update($wpdb->prefix.'icl_strings', ['value' => $originalPage->post_name], ['id' => $mainString->id]);

                            foreach ($this->getTranslations($pageID) as $translation){
                                if($translation->source_language_code){
                                    $translatedPage = get_post($translation->element_id);
                                    if($translatedPage){
                                        $wpdb->update($wpdb->prefix.'icl_string_translations', ['value' => $translatedPage->post_name], ['string_id' => $mainString->id, 'language' => $translation->language_code]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    /**
     * Save option page translations as slug in WPML
     * @return void
     */
    public function autoRegisterLanguagesSlugs ($pageID, $page)
    {
        $currentLanguage = $this->getCurrentLanguage();
        if($page->post_type == 'page' && $currentLanguage) {

            // Get default language page
            $originalTranslation = null;
            foreach ($this->getTranslations($pageID) as $translation){
                if(!$translation->source_language_code){
                    $originalTranslation = $translation;
                }
            }

            if($originalTranslation){
                $originalPage = get_post($originalTranslation->element_id);
                foreach ($this->postTypes as $postType) {
                    $option = $this->optionPrefix.$postType->name;
                    $optionID = get_option($option);

                    // If post type option archive page is equal to $default language page
                    if($optionID == $originalPage->ID){

                        // Update slugs of each translation
                        foreach ($this->getTranslations($pageID) as $translation){
                            if($translation->source_language_code){
                                $translationPage = get_post($translation->element_id);
                                $this->setTranslatedSlug($translationPage->post_name, $postType->name, $originalTranslation->language_code, $translation->language_code);
                            }

                            if(!$translation->source_language_code){
                                $translationPage = get_post($translation->element_id);
                                $this->setMainSlug($translationPage->post_name, $postType->name, $originalTranslation->language_code);
                            }
                        }
                    }
                }

                flush_rewrite_rules();
            }
        }
    }

    /**
     * Define WPML Custom Post Type slugs in DB
     *
     * @param $translatedSlug
     * @param $postType
     * @param $defaultLanguage
     * @param $language
     * @return void
     */
    public function setTranslatedSlug($translatedSlug, $postType, $defaultLanguage, $language)
    {
        global $wpdb;

        $mainString = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}icl_strings WHERE language = '$defaultLanguage' AND name = 'URL slug: $postType'");
        if($mainString){
            $wpdb->update($wpdb->prefix.'icl_string_translations', ['value' => $translatedSlug], ['string_id' => $mainString->id, 'language' => $language]);
        }
    }

    /**
     * Define WPML default language Custom Post Type slug in DB
     *
     * @param $defaultSlug
     * @param $postType
     * @param $defaultLanguage
     * @return void
     */
    public function setMainSlug($defaultSlug, $postType, $defaultLanguage)
    {
        global $wpdb;

        $mainString = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}icl_strings WHERE language = '$defaultLanguage' AND name = 'URL slug: $postType'");
        if($mainString){
            $wpdb->update($wpdb->prefix.'icl_strings', ['value' => $defaultSlug], ['id' => $mainString->id]);
        }
    }

    /**
     * Redirect user when archive is accessed directly and if page is child of another
     * @param $page
     * @param $postType
     * @return void
     */
    public function addMultilingualRedirects($page, $postType)
    {
        $translations = $this->getTranslations($page->ID);

        foreach ($translations as $translation){

            $permalink = apply_filters('wpml_permalink', get_post_type_archive_link($postType->name), $translation->language_code, true);
            $currentURL = $this->getCurrentURL();
            $pageLink = ($page) ? get_permalink($page->ID) : null;

            if($currentURL && ($pageLink && $permalink == $currentURL['url'] && $permalink != $pageLink)){

                if($currentURL['query']){
                    $pageLink = $pageLink.'?'.$currentURL['query'];
                }

                wp_redirect($pageLink, 301);

            }
        }

    }

    /**
     * Add Rewrite rules for custom post types and for all languages of the page
     *
     * @param $page
     * @param $postType
     * @return void
     */
    public function addMultilingualRewriteRules($page, $postType)
    {
        global $wp_rewrite;
        $translations = $this->getTranslations($page->ID);

        foreach ($translations as $translation){

            $wpml_permalink = apply_filters('wpml_permalink', get_permalink($translation->element_id), $translation->language_code, true);
            $wpml_home      = apply_filters('wpml_permalink', get_permalink(get_option('page_on_front')), $translation->language_code);
            $archive_slug   = trim(str_replace($wpml_home, '', $wpml_permalink), '/');

            add_rewrite_rule( "{$archive_slug}/?$", "index.php?post_type=$postType->name", 'top' );
            if($postType->rewrite['pages']){
                add_rewrite_rule("{$archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$", "index.php?post_type=$postType->name" . '&paged=$matches[1]', 'top');
            }
        }
    }

}