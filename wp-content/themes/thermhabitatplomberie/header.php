<!doctype html>
<html lang="fr">
<head>

    <!-- General Metas -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, viewport-fit=cover">

    <!-- WordPress -->
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>

    <?php
    $logo = get_field('logo-site', 'option') ? get_field('logo-site', 'option')['ID'] : get_theme_mod('custom_logo');
    ?>

    <header id="header-site">

        <div class="container">

            <?php if ($logo || $logoMobile): ?>
                <a href="<?php echo get_home_url(); ?>" class="logo-container">
                    <?php echo wp_get_attachment_image($logo, 'full'); ?>
                </a>
            <?php endif; ?>


            <?php
            wp_nav_menu([
                'theme_location' => 'main_navigation',
                'container_class' => 'main-navigation',
                'menu_class' => 'main-menu',
                'items_wrap' => '<ul class="%2$s">%3$s</ul>',
            ]);
            ?>

            <?php get_template_part('template-parts/elements/burger'); ?>


        </div>

    </header>