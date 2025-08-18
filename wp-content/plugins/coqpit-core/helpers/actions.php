<?php

if(!function_exists('get_post_action_url')){
    /**
     * Get form post action url
     * @return string|null
     */
    function get_post_action_url(){

        return \COQPIT\Plugins\Core\Actions\Post::getFormAction();

    }
}

if(!function_exists('get_post_errors')){

    /**
     * Get each form errors as array
     * @return array
     */
    function get_post_errors(){

        if(isset($_SESSION) &&
            isset($_SESSION['errors']) &&
            is_array($_SESSION['errors']) &&
            count($_SESSION['errors'])
        ){
            return $_SESSION['errors'];
        }

        return [];

    }
}

if(!function_exists('get_post_error')){

    /**
     * Get specific error based on input name
     * @param $name
     * @return mixed|null
     */
    function get_post_error($name){

        if(
            isset($_SESSION) &&
            isset($_SESSION['errors']) &&
            is_array($_SESSION['errors']) &&
            count($_SESSION['errors']) &&
            isset($_SESSION['errors'][$name])
        ){
            return $_SESSION['errors'][$name];
        }

        return null;

    }
}

if(!function_exists('get_post_data')){

    /**
     * Get posted data like $_POST
     * @return array
     */
    function get_post_data(){

        if(
            isset($_SESSION) &&
            isset($_SESSION['data']) &&
            is_array($_SESSION['data']) &&
            count($_SESSION['data'])
        ){
            return $_SESSION['data'];
        }

        return [];

    }
}

if(!function_exists('define_post_notice')){

    /**
     * Define notice message
     * @param $value
     * @return void
     */
    function define_post_notice($type, $message){

        if(isset($_SESSION)){
            $_SESSION['notice'] = [
                'type'      => $type,
                'message'   => $message
            ];
        }

    }
}

if(!function_exists('get_post_notice')){

    /**
     * Get notice message
     * @return void
     */
    function get_post_notice($key = null){

        $hasNotice = (isset($_SESSION) && isset($_SESSION['notice']));

        if($key == 'message' && $hasNotice && isset($_SESSION['notice']['message'])){
            return $_SESSION['notice']['message'];
        }

        if($key == 'type' && $hasNotice && isset($_SESSION['notice']['type'])){
            return $_SESSION['notice']['type'];
        }

        if($hasNotice){
            return $_SESSION['notice'];
        }

        return null;

    }
}