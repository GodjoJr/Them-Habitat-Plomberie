<?php defined('ABSPATH') || die('Nope'); ?>

<?php

$image = get_field('homepage-banner-image');
$title = wp_kses_post(get_field('homepage-banner-title'));
$text = wp_kses_post(get_field('homepage-banner-text'));
$button = get_field('homepage-banner-button');
$margin = get_field('homepage-banner-margin')['margin'] ?: 0;

?>

<section id="homepage-banner" style="margin-bottom: <?= $margin; ?>px;">

    <div class="container">

        <div class="left-container">

            <?php if (!empty($title)): ?>
                <h1 class="htitle"><?= $title; ?></h1>
            <?php endif; ?>

            <?php if (!empty($text)): ?>
                <div class="text-container">
                    <?= $text; ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($button)) get_template_part('template-parts/elements/button', null, ['button' => $button]); ?>

        </div>

        <div class="right-container">

            <?php if (!empty($image)): ?>
                <div class="image-container">
                    <?= wp_get_attachment_image($image['ID'], 'full'); ?>
                </div>
            <?php endif; ?>
            
        </div>

    </div>


</section>