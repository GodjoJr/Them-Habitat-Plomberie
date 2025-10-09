<?php defined('ABSPATH') || die('Nope'); ?>

<?php

$id = $args['id'];

if (empty($id))
    return;


$icon = get_field('service-icon', $id);
$title = get_the_title($id);
$description = get_field('service-description', $id);
$permalink = get_the_permalink($id);

?>

<article class="service-card">
    <a href="<?= $permalink; ?>">

        <?php if (!empty($icon)): ?>
            <div class="icon">
                <?= extractSvg($icon['url']); ?>
            </div>
        <?php endif; ?>

        <h3 class="title"><?= $title; ?></h3>

        <?php if (!empty($description)): ?>
            <div class="description">
                <?= $description; ?>
            </div>
        <?php endif; ?>

    </a>
</article>