<?php defined('ABSPATH') || die('Nope'); ?>

<?php

$overtitle = wp_kses_post($block['overtitle']);
$title = wp_kses_post($block['title']);
$timeline = $block['timeline'];
$margin = $block['margin'] ?: 0;

?>

<section class="block-timeline" style="margin-bottom: <?= $margin ?>px;">

    <div class="container">

        <?php if (!empty($overtitle)): ?>
            <p class="overtitle"><?= $overtitle; ?></p>
        <?php endif; ?>

        <?php if (!empty($title)): ?>
            <h2 class="htitle"><?= $title; ?></h2>
        <?php endif; ?>

        <?php if (!empty($timeline)): ?>
            <div class="timeline-container">

                <?php foreach ($timeline as $item): ?>
                    <div class="timeline-item">

                        <div class="top-container">

                            <?php if (!empty($item['icon'])): ?>
                                <div class="icon">
                                    <?= extractSvg($item['icon']['url']); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($item['title'])): ?>
                                <h3 class="title"><?= $item['title']; ?></h3>
                            <?php endif; ?>

                        </div>

                        <?php if (!empty($item['text'])): ?>
                            <div class="text-container"><?= $item['text']; ?></div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>
    </div>

</section>