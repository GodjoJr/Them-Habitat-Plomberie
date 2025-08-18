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

<?php $picture = get_theme_mod('coqpit_maintenance_picture'); ?>

<div class="maintenance" style="text-align: center;">
    <?php if($picture): ?><p><img src="<?php echo get_theme_mod('coqpit_maintenance_picture'); ?>" alt="<?php echo get_theme_mod('coqpit_maintenance_title'); ?>"></p><?php endif; ?>
    <p><?php echo get_theme_mod('coqpit_maintenance_title'); ?></p>
    <p><?php echo get_theme_mod('coqpit_maintenance_description'); ?></p>
</div>

<?php wp_footer(); ?>
</body>
</html>