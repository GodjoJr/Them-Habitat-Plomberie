<?php

namespace COQPIT\Plugins\Core\CustomFields;


class Menus {

    /**
     * Add ACF Rules for menu items (depth)
     * @return void
     */
    public function init () :void
    {
        add_filter('acf/location/rule_types',               [$this, 'addRuleType']);
        add_filter('acf/location/rule_values/menu_level',   [$this, 'addRuleValues']);
        add_filter('acf/location/rule_match/menu_level',    [$this, 'addRuleMatches'], 10, 4);
    }

    /**
     * Add ACF Rule type
     * @return array
     */
    public function addRuleType($choices) :array
    {
        $choices['Menu']['menu_level'] = __('Niveau du menu', 'coqpit-core');

        return $choices;
    }

    /**
     * Add ACF Rule values
     * @return array
     */
    public function addRuleValues($choices) :array
    {

        for($i = 0; $i <= 5; $i++){
            $choices[$i] = $i;
        }

        return $choices;
    }

    /**
     * Add ACF Rule Matches (verification)
     * @return bool
     */
    public function addRuleMatches($match, $rule, $options, $field_group) :bool
    {

        if ($rule['operator'] == "==" && isset($options['nav_menu_item_depth'])) {
            $match = ($options['nav_menu_item_depth'] == $rule['value']);
        }

        return $match;
    }

}
