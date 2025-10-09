<?php

$logo = get_field('logo-site-footer', 'option') ? get_field('logo-site-footer', 'option')['ID'] : get_theme_mod('custom_logo');
$email = wp_kses_post(get_field('email', 'option'));
$telephone = wp_kses_post(get_field('telephone', 'option'));
$socials = get_field('socials', 'option');
$address = get_field('address', 'option');
$displayCredits = get_field('display-credits', 'option');

?>

<footer id="footer-site">

    <div class="container">

        <div class="top-container">

            <div class="left-container">

                <?php if (!empty($logo)): ?>
                        <a href="<?php echo get_home_url(); ?>" class="logo-container">
                            <?php echo wp_get_attachment_image($logo, 'full'); ?>
                        </a>
                <?php endif; ?>

                <?php if (!empty($socials)): ?>
                    <div class="socials">
                        <?php foreach ($socials as $social): ?>
                            <a href="<?= $social['link']; ?>" class="logo-container social">
                                <?= extractSvg($social['icon']['url']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

            <div class="right-container">

                <?php
                wp_nav_menu([
                    'theme_location' => 'footer_navigation',
                    'container_class' => 'footer-navigation',
                    'menu_class' => 'main-menu',
                    'items_wrap' => '<ul class="%2$s">%3$s</ul>',
                ]);
                ?>

                <div class="infos">

                    <p class="title"><?= _x('Nos coordonnées', 'Footer Informations', 'thp'); ?></p>

                    <?php if (!empty($email)): ?>
                        <div class="email">
                            <a href="mailto:<?= $email; ?>"><?= $email; ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($telephone)): ?>
                        <div class="telephone">
                            <a href="tel:<?= $telephone; ?>"><?= $telephone; ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($address)): ?>
                        <div class="address">
                            <p><?= $address['street']; ?></p>
                            <p><?= $address['zip']; ?>     <?= $address['city']; ?></p>
                        </div>
                    <?php endif; ?>

                </div>

            </div>

        </div>

        <div class="bottom-container">

            <?php if ($displayCredits): ?>
                <p class="credits">
                    <?php echo _x('Therm Habitat Plomberie - Tous droits réservés', 'Footer Informations', 'thp'); ?>
                </p>
            <?php endif; ?>

            <?php
            wp_nav_menu([
                'theme_location' => 'legal_navigation',
                'container_class' => 'legal-navigation',
                'menu_class' => 'main-menu',
                'items_wrap' => '<ul class="%2$s">%3$s</ul>',
            ]);
            ?>

        </div>

    </div>

</footer>

<?php wp_footer(); ?>
</body>

</html>