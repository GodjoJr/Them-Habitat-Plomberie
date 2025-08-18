<?php

namespace COQPIT\Plugins\ThpCore\WordPress;

use COQPIT\Plugins\Core\Template\Gutenberg;
use COQPIT\Plugins\Core\Template\Menus;
use COQPIT\Plugins\Core\Template\StaticPage;

class CMS
{
    public function __construct()
    {
        Gutenberg::disable();

        $this->registerNavigations();
        $this->registerStaticPages();

        /**
         * Allow pagination in custom post types
         */
        add_action('init', function () {
            add_rewrite_rule('(.?.+?)/page/?([0-9]{1,})/?$', 'index.php?pagename=$matches[1]&paged=$matches[2]', 'top');
        });

        add_filter('wp_loaded', function () {
            if (in_array('contact-form-7/wp-contact-form-7.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                add_action('wp_enqueue_scripts', [$this, 'deregister_recaptcha_on_all_pages'], 20);
                add_action('wpcf7_contact_form', [$this, 'load_recaptcha_conditionally']);
            }
        });

        add_filter('tiny_mce_before_init', array($this, 'custom_tinymce_format_options'));

    }

    function registerNavigations()
    {
        Menus::register([
            'main_navigation' => __('Navigation Principale', 'thp-core'),
            'footer_navigation' => __('Navigation Pied de page', 'thp-core'),
            'legal_navigation' => __('Navigation Légale', 'thp-core'),
        ]);
    }

    function registerStaticPages()
    {
        StaticPage::add('contact_page', __('Page de contact', 'thp-core'), 'contact');
    }

    // Function to deregister reCAPTCHA site-wide and load it only when a form exists
    function deregister_recaptcha_on_all_pages()
    {
        // Deregister the reCAPTCHA script
        wp_dequeue_script('google-recaptcha');
        wp_dequeue_script('wpcf7-recaptcha');
    }

    // Function to conditionally load reCAPTCHA on pages with Contact Form 7 forms
    function load_recaptcha_conditionally()
    {
        add_action('wp_footer', function () {
            // Enqueue the reCAPTCHA script if a form is found
            wp_enqueue_script('google-recaptcha');
            wp_enqueue_script('wpcf7-recaptcha');
        });
    }

    function custom_tinymce_format_options($initArray)
    {
        // Formats disponibles dans TinyMCE
        $initArray['block_formats'] = 'Paragraph=p; Heading 2=h2; Heading 3=h3';
        return $initArray;
    }
}
