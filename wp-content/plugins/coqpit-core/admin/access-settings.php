<div class="wrap">
    <h1><?php _e('Réglages des accès', 'coqpit-core'); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('access');
        do_action('before_access_settings_page');
        do_settings_sections('access');
        do_action('after_access_settings_page');
        ?>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Enregistrer les modifications', 'coqpit-core'); ?>"></p>
    </form>
</div>