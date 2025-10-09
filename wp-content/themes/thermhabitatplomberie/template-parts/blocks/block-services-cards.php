<?php defined('ABSPATH') || die('Nope'); ?>

<?php

$overtitle = wp_kses_post($block['overtitle']);
$title = wp_kses_post($block['title']);
$services = $block['services'];
$margin = $block['margin'] ?: 0;
?>

<section class="block-services-cards" style="margin-bottom: <?= $margin ?>px;">

    <div class="container">

        <?php if (!empty($overtitle)): ?>
            <p class="overtitle"><?= $overtitle; ?></p>
        <?php endif; ?>

        <?php if (!empty($title)): ?>
            <h2 class="htitle"><?= $title; ?></h2>
        <?php endif; ?>

        <?php if (!empty($services)): ?>
            <div class="cards">
                <?php foreach ($services as $service)
                    get_template_part('template-parts/elements/service-card', '', ['id' => $service]); ?>
            </div>
        <?php endif; ?>

    </div>

</section>