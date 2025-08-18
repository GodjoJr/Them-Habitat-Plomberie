<?php

namespace COQPIT\Plugins\Core\Actions;

use COQPIT\Core\Vendor\Rakit\Validation\Validator;

class Ajax {

    /**
     * Add an ajax action with integrated validation rules and nonce verification
     *
     * @param $actionName
     * @param $onlyLoggedInUsers
     * @param $callback
     * @param $validationRules
     * @param $validationMessages
     * @return void
     */
    public static function addAction($actionName, $onlyLoggedInUsers, $callback, $validationRules = [], $validationMessages = [], $adminAction = false)
    {

        $action = [
            'name'      => $actionName,
            'callback'  => $callback,
            'rules'     => $validationRules,
            'messages'  => $validationMessages,
            'admin'     => $adminAction
        ];

        $action = apply_filters('coqpit_ajax_action_data', $action);

        // Logged In users
        add_action('wp_ajax_'.$actionName, function () use ($action) {

            if($action['admin'] && is_admin()){
                do_action('admin_ajax_action_execute', $action['callback'], $action['rules'], $action['messages']);
            } else {
                if (!wp_verify_nonce($_POST['nonce'])) {
                    wp_send_json_error(['message' => __('Vous n’avez pas l’autorisation d’effectuer cette action.', 'coqpit-core')], 403 );
                } else {
                    do_action('admin_ajax_action_execute', $action['callback'], $action['rules'], $action['messages']);
                }
            }

        });

        add_action('admin_ajax_action_execute', [self::class, 'execute'], 1, 3);

        if(!$onlyLoggedInUsers){
            // Guest users
            add_action('wp_ajax_nopriv_'.$actionName, function () use ($action) {

                if($action['admin'] && is_admin()){
                    do_action('ajax_action_execute', $action['callback'], $action['rules'], $action['messages']);
                } else {
                    if (!$action['admin'] && !wp_verify_nonce($_POST['nonce'])) {
                        wp_send_json_error(['message' => __('Vous n’avez pas l’autorisation d’effectuer cette action.', 'coqpit-core')], 403);
                    } else {
                        do_action('ajax_action_execute', $action['callback'], $action['rules'], $action['messages']);
                    }
                }

            });

            add_action('ajax_action_execute', [self::class, 'execute'], 1, 3);
        }
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

        if(is_array($validation)){
            wp_send_json_error( ['message' => __('La validation des données a échouée.', 'coqpit-core'), 'errors' => $validation], 422 );
        } else if(is_bool($validation) && $validation == true){
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

}
