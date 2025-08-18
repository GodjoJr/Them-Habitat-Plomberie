<?php

namespace COQPIT\Plugins\Core\WordPress;

class Hook {

    /**
     * @param string $actionName
     * @param mixed ...$args
     * @return void
     */
    public static function makeAction (string $actionName, mixed ...$args) :void
    {
        do_action($actionName, ...$args);
    }

    /**
     * @param string $actionName
     * @param mixed $callback
     * @param int $priority
     * @param int $acceptedArguments
     * @return void
     */
    public static function executeAction (string $actionName, mixed $callback, int $priority = 10, int $acceptedArguments = 1) :void
    {
        add_action($actionName, $callback, $priority, $acceptedArguments);
    }

    /**
     * Create filter for specific value
     *
     * @param string $actionName
     * @param mixed $value
     * @param mixed ...$args
     * @return mixed
     */
    public static function makeFilter (string $actionName, mixed $value, mixed ...$args) :mixed
    {
        return apply_filters($actionName, $value, ...$args);
    }

    /**
     * Execute filter defined with Hook::makeFilter()
     *
     * @param string $actionName
     * @param mixed $callback
     * @param int $priority
     * @param int $acceptedArguments
     * @return mixed
     */
    public static function executeFilter (string $actionName,  mixed $callback, int $priority = 10, int $acceptedArguments = 1) :mixed
    {
        return add_filter($actionName, $callback, $priority, $acceptedArguments);
    }

}
