<?php

get_header();

get_template_part('template-parts/banners/homepage-banner');

?>

<div class="homepage-blocks">
    <?php
    $blocks = get_field('content-blocks', get_the_ID());
    if ($blocks) {
        foreach ($blocks as $block) {
            set_query_var('block', $block);
            echo get_template_part('template-parts/blocks/block-' . $block['acf_fc_layout']);
        }
    }
    ?>
</div>

<?php
get_footer();