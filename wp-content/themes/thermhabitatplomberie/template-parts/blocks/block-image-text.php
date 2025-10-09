<?php defined('ABSPATH') || die('Nope'); ?>

<?php

$reversedColumns = $block['reversed-columns'] ? ' reversed-columns' : '';
$coloredBackground = $block['colored-background'] ? ' colored-background' : '';

$images = $block['images'];

$overtitle = wp_kses_post($block['overtitle']);
$title = wp_kses_post($block['title']);
$text = wp_kses_post($block['text']);

$tags = $block['tags'];

$button = $block['button'];

$margin = $block['margin'] ?: 0;

?>

<section class="block-image-text<?= $reversedColumns; ?><?= $coloredBackground; ?>"
    style="margin-bottom: <?= $margin; ?>px;">

    <div class="container">

        <?php if (!empty($images)): ?>
            <div class="left-container">
                <?php foreach ($images as $image): ?>
                    <div class="image-container">
                        <?= wp_get_attachment_image($image['ID'], 'full'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="right-container">
            <?php if (!empty($overtitle)): ?>
                <p class="overtitle"><?= $overtitle; ?></p>
            <?php endif; ?>

            <?php if (!empty($title)): ?>
                <h2 class="htitle"><?= $title; ?></h2>
            <?php endif; ?>

            <?php if (!empty($text)): ?>
                <div class="text-container"><?= $text; ?></div>
            <?php endif; ?>

            <?php if (!empty($tags)): ?>
                <div class="tags-container">

                    <?php foreach ($tags as $tag): ?>
                        <div class="tag">

                            <?php if ($tag['title']): ?>
                                <p class="tag-title"><?= $tag['title']; ?></p>
                            <?php endif; ?>

                            <?php if ($tag['text']): ?>
                                <p class="tag-text"><?= $tag['text']; ?></p>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>

                </div>
            <?php endif; ?>

            <?php if (!empty($button)): ?>
                <a href="<?= esc_url($button['url']); ?>" class="btn btn-primary"
                    target="<?= esc_attr($button['target']); ?>">
                    <div class="icon"><?= get_template_part('template-parts/svg/arrow-right') ?></div><?= esc_html($button['title']); ?>
                </a>
            <?php endif; ?>

        </div>


</section>