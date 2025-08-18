<?php

if(!function_exists('get_option_page_id')){
    /**
     * Get option page id
     * @return string|null
     */
    function get_option_page_id($option){
        $language = new \COQPIT\Plugins\Core\Multilingual\Language();
        if($language->getCurrentLanguage()){
            return $language->getOptionPageID($option);
        }

        return get_option($option);
    }
}

if(!function_exists('get_option_page_link')){
    /**
     * Get option page permalink
     * @return string|null
     */
    function get_option_page_link($option){
        return get_permalink(get_option_page_id($option));
    }
}