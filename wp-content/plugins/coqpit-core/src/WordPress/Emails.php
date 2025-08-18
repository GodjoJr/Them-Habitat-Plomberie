<?php

namespace COQPIT\Plugins\Core\WordPress;

use PHPMailer\PHPMailer\PHPMailer;

class Emails {

    public function init()
    {
        add_action('admin_menu',        [$this, 'optionPage']);
        add_action('admin_init',        [$this, 'emailsOptions']);
        add_action('admin_init',        [$this, 'testEmail'], 999);
        add_action('phpmailer_init',    [$this, 'mailerConfiguration']);
    }

    public function testEmail()
    {
        if(current_user_can('edit_emails_settings')) {
            if (isset($_GET['send-email-test'])) {
                $this->sendTestEmail();
                wp_redirect('options-general.php?page=emails');
            }
        }
    }

    public function optionPage()
    {
        if(current_user_can('edit_emails_settings')) {
            add_options_page(
                __('Réglages des emails', 'coqpit-core'),
                __('Emails', 'coqpit-core'),
                'manage_options',
                'emails',
                [$this, 'optionPageInner']
            );
        }
    }

    public function optionPageInner()
    {
        echo '<div class="wrap">';
            echo '<h1>'.__('Réglages des emails', 'coqpit-core').'</h1>';
            echo '<form method="post" action="options.php">';
                settings_fields( 'emails' );
                do_settings_sections( 'emails' );
                echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="'.__('Enregistrer les modifications', 'coqpit-core').'">';
                echo '<a style="margin-left: 20px;" href="options-general.php?page=emails&send-email-test=true" class="button button-primary">Envoyer un email de test</a>';
                echo '</p>';
            echo '</form>';;
        echo '</div>';
    }

    public function emailsOptions()
    {
        if(current_user_can('edit_emails_settings')) {

            add_settings_section('smtp', __('Serveur SMTP', 'coqpit-core'), '__return_false', 'emails');

            register_setting('emails', 'coqpit_emails_smtp_enabled', [
                'type' => 'boolean',
                'default' => false,
            ]);

            add_settings_field('coqpit-emails-smtp-enabled-field', __('Activer', 'coqpit-core'), [$this, 'renderEnabledSetting'], 'emails', 'smtp', [
                'label_for' => 'coqpit_emails_smtp_enabled'
            ]);

            register_setting('emails', 'coqpit_emails_smtp_from', [
                'type' => 'string',
                'default' => null,
            ]);

            add_settings_field('coqpit-emails-smtp-from-field', __('Email d\'envoi', 'coqpit-core'), [$this, 'renderFromSetting'], 'emails', 'smtp', [
                'label_for' => 'coqpit_emails_smtp_from'
            ]);

            register_setting('emails', 'coqpit_emails_smtp_host', [
                'type' => 'string',
                'default' => '127.0.0.1',
            ]);

            add_settings_field('coqpit-emails-smtp-host-field', __('Hôte', 'coqpit-core'), [$this, 'renderServerSetting'], 'emails', 'smtp', [
                'label_for' => 'coqpit_emails_smtp_host'
            ]);

            register_setting('emails', 'coqpit_emails_smtp_secure', [
                'type' => 'integer',
                'default' => 'ssl',
            ]);

            add_settings_field('coqpit-emails-smtp-secure-field', __('Méthode d\'encryptage', 'coqpit-core'), [$this, 'renderSecureSetting'], 'emails', 'smtp', [
                'label_for' => 'coqpit_emails_smtp_secure'
            ]);

            register_setting('emails', 'coqpit_emails_smtp_port', [
                'type' => 'integer',
                'default' => 587,
            ]);

            add_settings_field('coqpit-emails-smtp-port-field', __('Port', 'coqpit-core'), [$this, 'renderPortSetting'], 'emails', 'smtp', [
                'label_for' => 'coqpit_emails_smtp_port'
            ]);

            register_setting('emails', 'coqpit_emails_smtp_username', [
                'type' => 'string',
                'default' => null,
            ]);

            add_settings_field('coqpit-emails-smtp-username-field', __('Nom d\'utilisateur', 'coqpit-core'), [$this, 'renderUsernameSetting'], 'emails', 'smtp', [
                'label_for' => 'coqpit_emails_smtp_username'
            ]);

            register_setting('emails', 'coqpit_emails_smtp_password', [
                'type' => 'string',
                'default' => null,
            ]);

            add_settings_field('coqpit-emails-smtp-password-field', __('Mot de passe', 'coqpit-core'), [$this, 'renderPasswordSetting'], 'emails', 'smtp', [
                'label_for' => 'coqpit_emails_smtp_password'
            ]);

            add_settings_section('advanced', __('Paramétres d\'affichage', 'coqpit-core'), '__return_false', 'emails');

            register_setting('emails', 'coqpit_emails_smtp_name', [
                'type' => 'string',
                'default' => null,
            ]);

            add_settings_field('coqpit-emails-smtp-name-field', __('Nom affiché', 'coqpit-core'), [$this, 'renderNameSetting'], 'emails', 'advanced', [
                'label_for' => 'coqpit_emails_smtp_name'
            ]);

            add_settings_section('emails', __('Tester l\'envoi d\'email', 'coqpit-core'), '__return_false', 'emails');

            register_setting('emails', 'coqpit_emails_smtp_test', [
                'type' => 'string',
                'default' => get_option('admin_email'),
            ]);

            add_settings_field('coqpit-emails-smtp-test-field', __('Adresse email', 'coqpit-core'), [$this, 'renderTestSetting'], 'emails', 'emails', [
                'label_for' => 'coqpit_emails_smtp_test'
            ]);

        }

    }

    public function renderEnabledSetting ()
    {
        $enabled = get_option( 'coqpit_emails_smtp_enabled' );
        $checked = ($enabled) ? ' checked="checked"' : '';
        echo '<label>';
        echo '<input id="coqpit-emails-smtp-enabled-field" name="coqpit_emails_smtp_enabled" type="checkbox"'.$checked.'/>';
        echo '<p style="max-width: 400px">'.__('Active ou désactive l\'utilisation du SMTP', 'coqpit-core').'</p>';
        echo '</label>';
    }

    public function renderFromSetting ()
    {
        echo '<input id="coqpit-emails-smtp-from-field" class="regular-text" name="coqpit_emails_smtp_from" autocomplete="one-time-code" type="email" value="' . get_option( 'coqpit_emails_smtp_from' ) . '" />';
        echo '<p class="description" style="max-width: 400px">'.__('Email utilisé pour envoyer le message (From), en général le nom d\'utilisateur SMTP si c\'est une adresse email.', 'coqpit-core').'</p>';
    }

    public function renderServerSetting ()
    {
        $host = (get_option( 'coqpit_emails_smtp_host' )) ?: '127.0.0.1';
        echo '<input id="coqpit-emails-smtp-host-field" class="regular-text" name="coqpit_emails_smtp_host" type="text" value="' . $host . '" />';
        echo '<p class="description" style="max-width: 400px">'.__('Hôte SMTP permettant l\'envoi des emails, souvent sous la forme d\'un nom de domaine ex : smtp.domain.com', 'coqpit-core').'</p>';
    }

    public function renderPortSetting ()
    {
        echo '<input id="coqpit-emails-smtp-port-field" class="small-text" name="coqpit_emails_smtp_port" type="number" min="1" max="9999" value="' . get_option( 'coqpit_emails_smtp_port' ) . '" />';
        echo '<p class="description" style="max-width: 400px">'.__('Port associé à l\'envoi des emails, en général : 25, 465 (SSL) ou 587 (TLS)', 'coqpit-core').'</p>';
    }

    public function renderSecureSetting ()
    {
        $options = ['ssl', 'tls'];
        $value = get_option('coqpit_emails_smtp_secure');
        echo '<select id="coqpit-emails-smtp-secure-field" name="coqpit_emails_smtp_secure"/>';
        echo '<option value="" '.selected($value, '').'>Aucune</option>';
        foreach ($options as $label){
            echo '<option value="'.$label.'" '.selected($value, $label).'>'.strtoupper($label).'</option>';
        }
        echo '</select>';
        echo '<p class="description" style="max-width: 400px">'.__('Défini la méthode d\'encryptage des données, en général port = 465 alors SSL ou bien port = 587 = TLS', 'coqpit-core').'</p>';
    }

    public function renderUsernameSetting ()
    {
        echo '<input id="coqpit-emails-smtp-username-field" class="regular-text" name="coqpit_emails_smtp_username" autocomplete="one-time-code" type="text" value="' . get_option( 'coqpit_emails_smtp_username' ) . '" />';
        echo '<p class="description" style="max-width: 400px">'.__('Nom d\'utilisateur SMTP permettant d\'identifier l\'envoi des emails. C\'est en général une adresse email. Laisser vide si le SMTP ne nécessite pas d\'authentification', 'coqpit-core').'</p>';
    }

    public function renderPasswordSetting ()
    {
        echo '<input id="coqpit-emails-smtp-password-field" class="regular-text" name="coqpit_emails_smtp_password" autocomplete="one-time-code" type="password" value="' . get_option( 'coqpit_emails_smtp_password' ) . '" />';
        echo '<p class="description" style="max-width: 400px">'.__('Mot de passe de l\'utilisateur SMTP permettant d\'identifier l\'envoi des emails. Laisser vide si le SMTP ne nécessite pas d\'authentification', 'coqpit-core').'</p>';
    }

    public function renderNameSetting ()
    {
        echo '<input id="coqpit-emails-smtp-name-field" class="regular-text" name="coqpit_emails_smtp_name" autocomplete="one-time-code" type="text" value="' . get_option( 'coqpit_emails_smtp_name' ) . '" />';
        echo '<p class="description" style="max-width: 400px">'.__('Nom affiché à l\'utilisateur lors de la réception de l\'email, laisser vide pour conserver la valeur par défaut de WordPress actuellement réglé sur : ', 'coqpit-core').get_bloginfo('name').'</p>';
    }

    public function renderTestSetting ()
    {
        $email = (get_option( 'coqpit_emails_smtp_test' )) ?: get_option( 'admin_email' );
        echo '<input id="coqpit-emails-smtp-test-field" class="regular-text" name="coqpit_emails_smtp_test" type="email" value="' . $email . '" />';
        echo '<p class="description" style="max-width: 400px">'.__('Adresse email permettant de vérifier que l\'envoi de l\'email s\'effectue correctement', 'coqpit-core').'</p>';
    }

    public function mailerConfiguration(PHPMailer $mailer)
    {
        $enabled = get_option('coqpit_emails_smtp_enabled');
        if($enabled){

            $name = get_option('coqpit_emails_smtp_name');
            $from = get_option('coqpit_emails_smtp_from');
            $username = get_option('coqpit_emails_smtp_username');
            $password = get_option('coqpit_emails_smtp_password');

            $mailer->isSMTP();
            $mailer->Host = (get_option( 'coqpit_emails_smtp_host' )) ?: '127.0.0.1';
            $mailer->Port = (get_option( 'coqpit_emails_smtp_port' )) ?: '25';
            $mailer->SMTPSecure = get_option('coqpit_emails_smtp_secure');

            if(!empty($username) && $username && !empty($password) && $password){
                $mailer->SMTPAuth = true;
                $mailer->Username = $username;
                $mailer->Password = $password;
            }

            if(!empty($name) && $name && !empty($from)){
                $mailer->setFrom($from, $name);
            } else if(!empty($name) && $name && !empty($username)){
                $mailer->setFrom($username, $name);
            } else if(!empty($name) && $name){
                $mailer->setFrom($mailer->From, $name);
            }

        }

    }

    public function sendTestEmail()
    {
        wp_mail(get_option('coqpit_emails_smtp_test'), __('[TEST] Email de vérification', 'coqpit-core'), sprintf(__('Ce message est envoyé uniquement dans le but de vérifier la validité de l\'envoi des emails sur votre site : %s', 'coqpit-core'), get_home_url()));
    }

}
