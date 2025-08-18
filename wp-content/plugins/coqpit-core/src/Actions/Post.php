<?php

namespace COQPIT\Plugins\Core\Actions;

use COQPIT\Core\Vendor\Rakit\Validation\Validator;

class Post {

    /**
     * Get form url for Post action submission
     * @return string|null
     */
    public static function getFormAction() :string|null
    {
        return admin_url( 'admin-post.php' );
    }

    /**
     * Add a post action (without ajax) with integrated validation rules and nonce verification
     *
     * @param $actionName
     * @param $onlyLoggedInUsers
     * @param $callback
     * @param $validationRules
     * @param $validationMessages
     * @return void
     */
    public static function addAction($actionName, $onlyLoggedInUsers, $callback, $validationRules = [], $validationMessages = [])
    {

        $action = [
            'name'      => $actionName,
            'callback'  => $callback,
            'rules'     => $validationRules,
            'messages'  => $validationMessages
        ];

        // Logged In users
        add_action('admin_post_'.$actionName, function () use ($action) {

            if (!wp_verify_nonce($_REQUEST['nonce'], $action['name'])) {
                wp_safe_redirect(wp_get_referer());
            } else {
                do_action('admin_post_action_execute', $action['callback'], $action['rules'], $action['messages']);
            }

        });

        add_action('admin_post_action_execute', [self::class, 'execute'], 1, 3);

        if(!$onlyLoggedInUsers){
            // Guest users
            add_action('admin_post_nopriv_'.$actionName, function () use ($action) {

                if (!wp_verify_nonce($_REQUEST['nonce'], $action['name'])) {
                    wp_safe_redirect(wp_get_referer());
                } else {
                    do_action('post_action_execute', $action['callback'], $action['rules'], $action['messages']);
                }

            });

            add_action('post_action_execute', [self::class, 'execute'], 1, 3);
        }

        add_action('wp_footer', [self::class, 'removeNotice'], 10);
    }

    /**
     * Execute action callback function and validate inputs
     *
     * @param $callback
     * @param $validationRules
     * @param $validationMessages
     * @return void
     */
    public static function execute ($callback, $validationRules, $validationMessages)
    {
        $validation = self::validate($validationRules, $validationMessages);

        if(is_array($validation) && isset($_SESSION)){
            $_SESSION['errors'] = $validation;
            $_SESSION['data'] = $_POST;
            wp_safe_redirect(wp_get_referer());
        } else if(is_bool($validation) && $validation == true && isset($_SESSION)){
            if(isset($_SESSION['errors'])) unset($_SESSION['errors']);
            if(isset($_SESSION['data'])) unset($_SESSION['data']);
            call_user_func($callback, $_POST);
        }
    }

    /**
     * Inputs validation
     *
     * @param $validationRules
     * @param $validationMessages
     * @return array|bool
     */
    public static function validate($validationRules, $validationMessages) :array|bool
    {
        if(is_array($validationRules) && count($validationRules)){

            $validator = new Validator();
            $validator = apply_filters('register_validation_rules', $validator);
            $validation = $validator->make($_POST + $_FILES, $validationRules);
            $validation->setMessages($validationMessages);
            $validation->validate();

            if($validation->fails()){

                $errors = [];

                foreach ($validation->errors()->toArray() as $key => $item){
                    $values = array_values($item);
                    $errors[$key] = $values[0];
                }

                $validation = $errors;

            } else {
                return true;
            }

        } else {
            return true;
        }

        return $validation;

    }

    /**
     * Remove notice if used
     * @return void
     */
    public static function removeNotice ()
    {
        if(isset($_SESSION) && isset($_SESSION['notice'])){
            unset($_SESSION['notice']);
        }

        if(isset($_SESSION) && isset($_SESSION['errors'])){
            unset($_SESSION['errors']);
        }

        if(isset($_SESSION) && isset($_SESSION['data'])){
            unset($_SESSION['data']);
        }
    }

}
