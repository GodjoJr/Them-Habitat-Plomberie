<?php

if (!function_exists('str_limit')) {
    function str_limit($value, $limit = 145, $end = '[…]')
    {
        /* Remove HTML Comments if exists */
        $value = preg_replace('/<!--(.|\s)*?-->/', '', $value);

        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth(html_entity_decode($value), 0, $limit, '', mb_internal_encoding())) . $end;
    }
}

if (!function_exists('displaySvg')) {
    /**
     * Allow to display SVG
     */
    function displaySvg($name)
    {
        $path = get_template_directory() . '/assets/svg/' . $name . '.svg';
        $svg = file_get_contents($path);

        return $svg;
    }
}

if (!function_exists('extractSvg')) {
    /**
     * Allow to display SVG from URL
     */
    function extractSvg($url)
    {
        $path = str_replace(get_home_url(), ABSPATH, $url);
        $svg = file_get_contents($path);

        return $svg;
    }
}

if (!function_exists('hex2rgba')) {
    /**
     * Convert Hex colors to RGBA
     */
    function hex2rgba($color, $opacity = false)
    {
        $defaultColor = 'rgb(0,0,0)';

        // Return default color if no color provided
        if (empty($color)) {
            return $defaultColor;
        }

        // Ignore "#" if provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        // Check if color has 6 or 3 characters, get values
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return $defaultColor;
        }

        // Convert hex values to rgb values
        $rgb = array_map('hexdec', $hex);

        // Check if opacity is set(rgba or rgb)
        if ($opacity >= 0) {
            if (abs($opacity) > 1) {
                $opacity = 1.0;
            }
            $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }

        // Return rgb(a) color string
        return $output;
    }
}

if (!function_exists('dateToTimestamp')) {
    /**
     * Change date format from d/m/Y to Timestamp
     */
    function dateToTimestamp($date)
    {
        $date_values = explode('/', $date);

        if (count($date_values) == 3) {
            $universal_date = $date_values[2] . '-' . $date_values[1] . '-' . $date_values[0];
            return strtotime($universal_date);
        }

        return false;
    }
}

if (!function_exists('cqt_get_template_part')) {
    function cqt_get_template_part($slug, $name = null)
    {

        do_action("cqt_get_template_part_{$slug}", $slug, $name);

        $templates = array();
        if (isset($name))
            $templates[] = "{$slug}-{$name}.php";

        $templates[] = "{$slug}.php";

        cqt_get_template_path($templates, true, false);
    }
}


if (!function_exists('cqt_get_template_path')) {
    function cqt_get_template_path($template_names, $load = false, $require_once = true)
    {
        $located = '';
        foreach ((array) $template_names as $template_name) {
            if (!$template_name)
                continue;

            if (file_exists($template_name)) {
                $located = $template_name;
                break;
            }
        }

        if ($load && '' != $located)
            load_template($located, $require_once);

        return $located;
    }
}

if (!function_exists('get_youtube_video')) {
    function get_youtube_video($youtube_link)
    {
        $param = parse_url($youtube_link)['query'];
        $exploded = explode('v=', $param)[1];
        $embed_value = explode('&', $exploded)[0];

        return $embed_value;
    }
}

if (!function_exists('obfuscateEmail')) {
    function obfuscateEmail($email)
    {
        $obfuscatedEmail = '';
        for ($i = 0; $i < strlen($email); $i++) {
            $obfuscatedEmail .= '&#' . ord($email[$i]) . ';';
        }
        return $obfuscatedEmail;
    }
}

if (!function_exists('renderStars')) {
    function renderStars($note, $max = 5)
    {
        $html = '<div class="stars">';

        $note = min($note, $max);

        $fullStars = floor($note);
        $decimal = $note - $fullStars;

        // Manage half star or extra star cases
        if ($decimal >= 0.75) {
            $fullStars++;
            $halfStars = 0;
        } elseif ($decimal >= 0.25) {
            $halfStars = 1;
        } else {
            $halfStars = 0;
        }

        $starCount = $fullStars + $halfStars;

        // Full stars
        for ($i = 0; $i < $fullStars; $i++) {
            $html .= displaySvg('full-star');
        }

        // Half star
        if ($halfStars) {
            $html .= displaySvg('half-star');
        }

        // Empty stars
        for ($i = $starCount; $i < $max; $i++) {
            $html .= displaySvg('empty-star');
        }

        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('get_all_registered_menus')) {
    function get_all_registered_menus()
    {
        $menus = wp_get_nav_menus();
        $menu_list = [];

        foreach ($menus as $menu) {
            $menu_list[] = [
                'ID' => $menu->term_id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'description' => $menu->description,
            ];
        }

        return $menu_list;
    }
}


if(!function_exists('addTitleStyle')) {
    function addTitleStyle($string)
    {
        return preg_replace('#/([^/]+)/#', '<span class="alternative">$1</span>', $string);
    }
}