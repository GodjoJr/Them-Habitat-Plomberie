# COQPIT Core - Documentation

## Changelog

Pour visualiser les modifications apportées à des dates précises, veuillez vous référer au [Changelog](CHANGELOG.md).

## Installation

Pour installer le plugin, télécharger le via le lien suivant : [Télécharger le plugin](https://git.coqpit.fr/plugins/wordpress/coqpit-core/-/archive/main/coqpit-core-main.zip)

Déposez le plugin dans le dossier des plugins de votre WordPress : `wp-content/plugins/`, renommez le dossier du plugin en `coqpit-core` et activez-le, vous êtes prêt à l'utiliser.

## Remarques

* Attention certaines fonctionnalités ne fonctionneront qu'une fois que les plugins correspondants seront actifs (Exemple: ACF Pro, Yoast SEO ou WPML).
* Certains cas de figure spécifiques peuvent présenter des anomalies notamment avec l'utilisation de WPML ou Elementor. Si c'est le cas, merci de [créer une issue sur Gitlab](https://git.coqpit.fr/plugins/wordpress/coqpit-core/-/issues).

## Fonctionnalités

* [Packages](#packages)
* [Hooks](#hooks)
* [Thème avec Laravel Mix](#theme-avec-laravel-mix)
* [Thème avec Vite](#theme-avec-vite)
* [Constantes WordPress](#constantes-wordpress)
* [Mises à jour des plugins et des thèmes WordPress](#mises-a-jour-des-plugins-et-des-themes-wordpress)
* [Fonctions/Helpers](#fonctionshelpers)
* [Templates](#templates)
* [Dashboard](#dashboard)
* [Blog](#blog)
* [Custom Post Types](#custom-post-types)
* [Scripts et Styles](#scripts-et-styles)
* [Menus](#menus)
* [Pages statiques](#pages-statiques)
* [Éditeur Gutenberg](#editeur-gutenberg)
* [Actions via Post](#actions-via-post)
* [Actions via Ajax](#actions-via-ajax)
* [ACF - Synchronisation](#acf-synchronisation)
* [ACF - Pages d'option](#acf-pages-doptions)
* [ACF - Règles](#acf-regles)
* [Elementor - Widgets](#elementor-widgets)
* [WPML - Multilingue](#wpml-multilingue)
* [Optimisations](#optimisations)
* [Mode Maintenance](#mode-maintenance)
* [Emails](#emails)
* [Accès](#accès)
* [Plugin starter](#plugin-starter)
* [Améliorations et ajouts prévus](#ameliorations-et-ajouts-prevus)
* [RGPD](#rgpd)

___

### Packages

Le plugin met à disposition plusieurs packages installés via Composer et permettant d'ajouter des fonctionnalités, voici la liste :
* `illuminate/support` - [Documentation](https://laravel.com/docs/10.x/helpers#strings-method-list) (Attention c'est une documentation Laravel, seul les méthodes permettant de gérer les "strings" sont à prendre en compte, celles qui commencent par `Str::`)
* `jenssegers/date` - [Documentation](https://github.com/jenssegers/date)
* `guzzlehttp/guzzle` - [Documentation](https://docs.guzzlephp.org/en/stable/)
* `symfony/var-dumper` - [Documentation](https://github.com/symfony/var-dumper)
* `rakit/validation` - [Documentation](https://github.com/rakit/validation)

Attention afin d'éviter les conflits avec d'autres plugins pouvant utiliser ces packages, il sera nécessaire de préfixer les namespaces lors de l'utilisation, comme par exemple :

```php
use Jenssegers\Date\Date;
```

qui deviendra :

```php
use COQPIT\Core\Vendor\Jenssegers\Date\Date;
```

___

### Hooks

Le plugin met à disposition plusieurs hooks vous permettant d'interagir avec le plugin 

#### Actions

```php
add_action('admin_ajax_action_execute', 'callback_function', 10, 2); // Action exécutée lors d'une requête ajax pour les utilisateurs connectés (2 paramètres disponibles $rules et $messages, ce hook survient avant la validation des données)
add_action('ajax_action_execute', 'callback_function', 10, 2); // Action exécutée lors d'une requête ajax pour les utilisateurs non connectés (2 paramètres disponibles $rules et $messages, ce hook survient avant la validation des données)
add_action('admin_post_action_execute', 'callback_function', 10, 2); // Action exécutée lors d'une requête post pour les utilisateurs connectés (2 paramètres disponibles $rules et $messages, ce hook survient avant la validation des données)
add_action('post_action_execute', 'callback_function', 10, 2); // Action exécutée lors d'une requête ajax pour les utilisateurs non connectés (2 paramètres disponibles $rules et $messages, ce hook survient avant la validation des données)
```

#### Filters

```php
apply_filters('coqpit_core_loaded', null /* Valeur par défaut */); // Peut être utilisé comme condition afin de savoir si le plugin COQPIT Core est chargé si c'est le cas la fonction return true.
add_filter('register_validation_rules', 'callback_function', 10, 1); // Filtre permettant d'ajouter des règles de validation personnalisées (voir la documentation sur les actions post et ajax
```

Si vous développez un plugin ayant pour but de fonctionner avec le plugin COQPIT Core, il est important que tout le plugin soit englobé dans le hook `coqpit_core_loaded`, exemple :

```php
if(apply_filters('coqpit_core_loaded', null)){
    \COQPIT\Plugins\Example\Example::init();
}
```

___

### Theme avec Laravel Mix

Un thème de base utilisant Laravel Mix est disponible dans les fichiers du plugin, pour l'utiliser, il vous suffit de copier/coller le dossier `./themes/template` dans le dossier `/wp-content/themes`.
Renommez le dossier et modifiez les informations dans le fichier `style.css` du thème nouvellement copié.

Activez le thème dans l'interface de WordPress pour commencer à l'utiliser.

Via le terminal, rendez-vous dans le dossier du thème et installez les dépendances en adaptant la commande ci-dessous :

```shell
cd {chemin du site}/wp-content/themes/template && npm install
```

Une fois les dépendances installées, il sera alors possible d'utiliser 3 commandes :

```shell
npm run dev # Compile les sources en mode "dev", les sources ne sont pas minifiées
npm run watch # Permet de lancer le watcher avec les sources en mode "dev". Voir le paragraphe suivant pour plus d'informations
npm run prod # Permet de compiler les sources en les minifiants pour réduire leur taille dans le but d'être utilisé en production
```

#### Le Watcher
Attention le watcher compile les sources sur un serveur node JS qui sera lancé automatiquement lors de l'exécution de la commande `npm run watch`, les sources seront alors accessible sur l'URL suivante : 
* `http://localhost:8080/assets/css/app.css`
* `http://localhost:8080/assets/js/app.js`

Si vous utilisez le [système d'inclusion des scripts et des styles](#scripts-et-styles) la bonne URL sera automatiquement récupérée que le watcher soit actif ou non.

Si le port est déjà utilisé sur votre machine un autre vous sera donné, faites donc attention à ce qui est écrit lorsque vous lancez la commande.
Dans le cas où le port est utilisé, renseignez la constante suivante dans le fichier `wp-config.php` :

```php
define('COQPIT_WATCHER_PORT', '8080');
```
Cela permettra au système d'enregistrement des scripts et des styles d'utiliser le port adéquat.

*Pour information, si vous ne compilez pas les sources à l'aide de la commande `npm run dev` ou `npm run prod` vos fichiers CSS et JS ne seront pas mis à jour sur votre projet. Si vous quittez le watcher et que vous rechargez votre page les données ne seront alors pas visibles.*

___

### Theme avec Vite

Un thème de base utilisant Vite.js est disponible dans les fichiers du plugin, pour l'utiliser, il vous suffit de copier/coller le dossier `./themes/vite` dans le dossier `/wp-content/themes`.
Renommez le dossier et modifiez les informations dans le fichier `style.css` du thème nouvellement copié.

Activez le thème dans l'interface de WordPress pour commencer à l'utiliser.

Via le terminal, rendez-vous dans le dossier du thème et installez les dépendances en adaptant la commande ci-dessous :

```shell
cd {chemin du site}/wp-content/themes/vite && npm install
```

Une fois les dépendances installées, il sera alors possible d'utiliser 2 commandes :

```shell
npm run dev # Lance un serveur de développement Node.js avec un rechargement automatique des données lors de la modification des fichiers js, scss et php
npm run prod # Permet de compiler les sources en les minifiants pour réduire leur taille dans le but d'être utilisé en production
```

#### Le Watcher
Attention le watcher compile les sources sur un serveur node JS qui sera lancé automatiquement lors de l'exécution de la commande `npm run dev`, les sources seront alors accessible sur l'URL suivante :
* `http://localhost:5173/resources/js/app.js`

Si vous utilisez le [système d'inclusion des scripts et des styles](#scripts-et-styles) la bonne URL sera automatiquement récupérée que le watcher soit actif ou non.

Si le port ou l'hôte sont déjà utilisés sur votre machine d'autre vous seront donné, faites donc attention à ce qui est écrit lorsque vous lancez la commande.
Dans le cas où le port et l'hôte sont utilisés, renseignez les constantes suivantes dans le fichier `wp-config.php` :

```php
define('WP_VITE_SERVER', 'http://localhost');
define('WP_VITE_PORT', '5173');
```
Cela permettra au système d'enregistrement des scripts et des styles d'utiliser le port adéquat.

*Pour information, si vous ne compilez pas les sources à l'aide de la commande `npm run prod` vos fichiers CSS et JS ne seront pas mis à jour sur votre projet. Si vous quittez le watcher et que vous rechargez votre page les données ne seront alors pas visibles.*

___

### Constantes WordPress

Ce plugin défini automatiquement certaines constantes si elles ne sont pas définies dans le fichier `wp-config.php`

Voici la liste des constantes automatiquement déclarées et leurs valeurs :
* WP_AUTO_UPDATE_CORE : `false`
* DISALLOW_FILE_EDIT : `true`
* FS_METHOD : `direct`
* WP_POST_REVISIONS : `5`
* EMPTY_TRASH_DAYS : `7`
* MEDIA_TRASH : `true`

___

### Theme support

Ce plugin défini automatiquement certains "supports" pour le theme

Voici la liste des "supports" automatiquement déclarés :

```php
add_theme_support('custom-logo');
add_theme_support('post-thumbnails');
add_theme_support('menus');
add_theme_support('woocommerce'); // Si WooCommerce est installé il sera automatiquement pris en compte
```

___

### Mises a jour des plugins et des themes WordPress

Ce plugin désactive automatiquement la mise à jour des plugins et des thèmes

___

### Fonctions/Helpers

Voici la liste des fonctions ajoutées et utilisables avec le plugin :
```php
/* WordPress */

get_option_page_id($optionName) // Permet de récupérer l'ID de la page d'option correspondante au nom de l'option utilisée, avec WPML donne l'ID de la page correspondant à l'option dans la langue courante
get_option_page_link($optionName) // Permet de récupérer le lien de la page d'option correspondante au nom de l'option utilisée, avec WPML donne le lien de la page correspondant à l'option dans la langue courante 

/* Debug */

dump($variable1, $variable2) // Permet d'effectuer un "var_dump" plus lisible à l'aide de Symfony Var Dumper
dd($variable1, $variable2) // Permet d'effectuer un "var_dump" plus lisible et un "die" à l'aide de Symfony Var Dumper
```

___

### Templates

Le système de hiérarchie des templates a été revu afin que vous puissiez utiliser celui de base de WordPress mais également un dossier nommé "templates" dans un souci d'organisation des fichiers du thème.

Vous trouverez ci-après une liste non exhaustive d'exemples :
* Page de base : `{theme}/templates/index.php`
* Homepage : `{theme}/templates/homepage.php`
* Single (Tout type de contenu) : `{theme}/templates/singular.php`
* Page du blog : `{theme}/templates/post/archive.php`
* Article du blog : `{theme}/templates/post/single.php`
* Catégorie d'articles du blog : `{theme}/templates/post/category.php`
* Archive de Custom Post Type : `{theme}/templates/event/archive.php`
* Single de Custom Post Type : `{theme}/templates/event/single.php` 

___

### Dashboard

Les Meta Box affichées sur le Dashboard sont toutes masquées à l'exception de la Meta Box COQPIT et de la Meta Box `dashboard_right_now`.
Il est cependant possible d'autoriser l'affichage de la Meta Box de votre choix via le code suivant à définir dans votre thème ou plugin :

```php
use COQPIT\Plugins\Core\WordPress\Dashboard;

Dashboard::allowMetaBox('META_BOX_ID');
```

La Meta Box "COQPIT - Informations" est affichée par défaut son contenu est modifiable via un hook défini dans le plugin

```php
add_filter('coqpit_dashboard_content', function($default_html){
    //Modifiez le contenu de la variable pour afficher ce que vous souhaitez (pour la mise en page, du CSS inline sera potentiellement nécessaire)
    return $default_html;
}, 20)
```

___

### Blog

Les permaliens des articles, des catégories et des étiquettes sont réécris afin que le slug de la page de blog soit ajouté automatiquement
Les schémas seront donc les suivants :
* `https://{site}/{blog_archive_slug}/{post_slug}`
* `https://{site}/{blog_archive_slug}/{category_base}/{category_slug}`
* `https://{site}/{blog_archive_slug}/{tag_base}/{tag_slug}`

Les variables `{category_base}` et `{tag_base}` sont à définir dans les paramètres des permaliens (préfixe des catégories et préfixe des étiquettes).
N'hésitez pas à recharger régulièrement les permaliens pour résoudre des problèmes d'accès.

___

### Custom Post Types

Pour créer des Custom Post Types il est désormais recommandé d'utiliser ACF pour créer les différents types de contenu et les taxonomies.

Il est possible d'utiliser les custom post types de la même manière que le blog, pour chaque type de contenu enregistré que ce soit via ACF ou de manière standard.

Le plugin ajoute automatiquement la possibilité de définir une page comme page d'archive dans le menu "Réglages" => "Lecture" de WordPress.

Le Slug de la page sera alors utilisé comme préfixe de slug pour les contenus, exemple : `https://{site}/{slug_de_page}/{post_slug}`

Il est également possible de récupérer l'ID de la page via le code suivant :
```php
$pageID = get_option_page_id('page_for_{custom_post_type}');
```
Et le lien de la page avec :
```php
$pageLink = get_option_page_link('page_for_{custom_post_type}');
```

___

### Scripts et Styles

#### Pour Laravel Mix et autres scripts

Voici un exemple d'utilisation : 
```php
use COQPIT\Plugins\Core\Template\Scripts; // Pour le template (Front-Office)
use COQPIT\Plugins\Core\Admin\Scripts; // Pour l'administration (Back-Office)

Scripts::register('nom-du-template-ou-du-plugin', [
    'assets/css/theme.css',
    'assets/js/theme.js'
], 
    get_template_directory_uri(), // URL du répertoire où se trouvent les fichiers
    get_template_directory(), // Chemin absolu du répertoire où se trouvent les fichiers
    true // Utilisation du Hot Module Replacement (HMR, rechargement CSS sans rechargement de la page) avec la nouvelle version de webpack.mix.js
);

// Ou

Scripts::register('nom-du-template-ou-du-plugin', [
    ['assets/css/theme.css', ['other-style', 'wc-style']],  // ['Chemin d'accès au fichier', [dépendances]]
    ['assets/js/theme.js', ['jquery', 'other-script']] // ['Chemin d'accès au fichier', [dépendances]]
], 
    get_template_directory_uri(), // URL du répertoire où se trouvent les fichiers
    get_template_directory(), // Chemin absolu du répertoire où se trouvent les fichiers
    true // Utilisation du Hot Module Replacement (HMR, rechargement CSS sans rechargement de la page) avec la nouvelle version de webpack.mix.js
);
```

La version est automatiquement définie sur la taille du fichier CSS ou JS, cela permet (lorsque les fichiers sont recompilés) d'éviter que l'ancien fichier reste en cache et qu'il soit nécessaire de vider le cache navigateur pour voir les modifications.

L'ordre dans lequel les fichiers JS et CSS sont ajoutés au tableau défini leur ordre de chargement, à faire attention lorsque vous utilisez des dépendances.

Si vous souhaitez utiliser de l'Ajax avec l'un des scripts, il sera nécessaire de le référencer de la manière suivante :

```php
use COQPIT\Plugins\Core\Template\Scripts;

Scripts::ajax([
    'assets/js/app.js',
    'assets/js/theme.js'
]);

// Ou

Scripts::ajax('assets/js/app.js');
```

Le nom de la variable Javascript s'adaptera au nom du script enregistré, si le fichier se nomme `theme.js` alors la variable sera `themeScriptAjax` si le fichier se nomme `app.js` alors la variable sera `appScriptAjax`.

```js
var themeScriptAjax = {
    "ajaxUrl": "https:\/\/wordpress.oo\/wp-admin\/admin-ajax.php",
    "nonce": "ef316029ae"
};
```

Un hook a été mis en place afin de vous permettre de gérer l'utilisation du script ou non. Il est par exemple possible de faire en sorte que les scripts et styles ne s'affichent que sur des pages définies. Voici comment ce hook s'utilise :

```php
add_filter('core_scripts_registration', function ($value, $scriptName){
    if($scriptName == 'nom-du-template-ou-du-plugin' && get_the_ID() != get_option('page_option')) return false;
    return $value;
}, 10, 2);
```

Sur cet exemple, le script nommé "nom-du-template-ou-du-plugin" ne se chargera que lorsque l'ID courant de la page est égal à la valeur de l'option "page_option" sinon il ne sera pas utilisé.

#### Pour Vite.js

* Le premier paramètre permet d'indiquer où se trouve le fichier JS que Vite va utiliser pour le développement
* Le second paramètre indique où se trouvera le fichier JSON contenant l'ensemble des informations de build et permettant l'ajout automatisé des fichiers dans WordPress (Ce fichier ne se génère que lors que vous utilisez la commande `npm run prod`)
* Le dernier paramètre de la fonction `register` permet de définir si le script utilisera de l'Ajax, dans le cas où cette variable est à `true` alors un objet javascript sera généré par WordPress juste avant l'import du fichier sur la page.

```php
use COQPIT\Plugins\Core\Template\Vite; // Pour le template (Front-Office) uniquement

Vite::register(__DIR__.'/resources/js/app.js', __DIR__.'/assets/manifest.json', true);
```

#### Utils.js

Des scripts basics sont mis à votre disposition dans le fichier nommé "utils.js", voici ce qu'ils permettent (veillez à bien vérifier si ces éléments sont susceptibles d'être utilisés avant d'importer une librairie pouvant être assez imposante en termes de poids) 

```js
import { documentReady } from './utils'

documentReady(() => {
  // Le DOM est chargé vous pouvez effectuer votre traitement Javascript ici
})

import { ajaxRequest } from './utils'

ajaxRequest('URL', 'NONCE', 'NOM DE L\'ACTION', {}).then((response) => {
  // La requête AJAX a réussie
}).catch((response) => {
  // Une erreur est survenue
})

import { debounce } from './utils'

input.addEventListener('input', debounce(() => {
  // Effectuer le traitement après une pause de 500ms entre chaque Event
  // Permet de temporiser une action et ainsi éviter d'effectuer un traitement à chaque changement
  // Ceci est un exemple
}, 500))
```

___

### Menus

Vous pouvez renseigner les menus utilisés dans votre thème en ajoutant du code spécifique au fichier `functions.php` de votre thème.

Voici un exemple d'utilisation :
```php
use COQPIT\Plugins\Core\Template\Menus;

Menus::register([
    'main_navigation'   => __('Navigation Principale', 'text-domain'),
    'footer_navigation' => __('Navigation Pied de page', 'text-domain')
]);
```

___

### Pages statiques

Dans le cas où vous souhaiteriez utiliser des pages statiques comme les pages de contact ou autre, il vous est possible de définir des pages statiques à l'aide de code spécifique à ajouter au fichier `functions.php` de votre thème.

Voici un exemple d'utilisation :
```php
use COQPIT\Plugins\Core\Template\StaticPage;

StaticPage::add('contact_page', __('Page de contact', 'text-domain'), 'contact');
```

* Paramètre n°1 : nom de l'option (sous forme de slug) afin de pouvoir récupérer l'ID de la page avec `get_option('contact_page')`;
* Paramètre n°2 : nom de la page
* Paramètre n°3 : nom du template de la page afin de pouvoir utiliser le [système de template](#templates) défini plus haut, dans cet exemple vous pourrez modifier la page en déposant un fichier nommé `contact.php` dans le bon répertoire, à savoir : `{theme}/templates/contact.php` 

Une fois la page déclarée, une option sera accessible dans le menu "Réglages" => "Lecture" de WordPress afin de spécifier quelle page WordPress correspond à notre page statique

Il est également possible de récupérer l'ID de la page via le code suivant :
```php
$pageID = get_option_page_id('contact_page');
```
Et le lien de la page avec :
```php
$pageLink = get_option_page_link('contact_page');
```

___

### Editeur Gutenberg

Si vous souhaitez désactiver l'éditeur Gutenberg pour certaines pages, il est possible d'utiliser du code spécifique en l'ajoutant au fichier `functions.php` de votre thème.

Voici un exemple d'utilisation :
```php
use COQPIT\Plugins\Core\Template\Gutenberg;

Gutenberg::disable(); // Désactive totalement Gutenberg
Gutenberg::disableForHomePage();
Gutenberg::disableForPostsPage();
Gutenberg::disableForPostID($id); // Fonctionne avec n'importe quel type de contenu
Gutenberg::disableForPostType('post_type'); // Désactive uniquement sur les posts du post type
Gutenberg::disableForStaticPage('option_page'); // Désactive uniquement sur les pages statiques
```

___

### Actions via Post

Dans le but de soumettre les données d'un formulaire sans nécessairement passer par une requête asynchrone (Ajax), il est possible d'utiliser du code spécifique en l'ajoutant au fichier `functions.php` de votre thème.

La méthode utilise 3 paramètres :
* Paramètre n°1 : Nom de l'action (sans caractères spéciaux, sans espaces en utilisant uniquement lettres, tirets et tirets bas)
* Paramètre n°2 : Est-ce que l'action doit utilisable uniquement par un utilisateur connecté ? (false : Disponible pour tout le monde, true : disponible uniquement pour les utilisateurs connectés)
* Paramètre n°3 : Fonction de callback permettant le traitement des données
* Paramètre n°4 : Règles de vérification des données, se référer à la [documentation](https://github.com/rakit/validation#usage) du package pour visualiser les différentes possibilités en terme de validation des données.

Voici un exemple d'utilisation :
```php
use COQPIT\Plugins\Core\Actions\Post;

Post::addAction('send_data', false, function ($data){

    // $data = les données soumises via le formulaire
    // Effectuez le traitement souhaité ici
    // Attention ce n'est pas de l'Ajax, une redirection vers l'URL souhaité sera nécessaire

}, [
    'firstname' => 'required',
    'lastname'  => 'required',
    'email'     => 'required|email'
], [
    'firstname:required'    => __('Le prénom est requis', 'text-domain'),
    'lastname:required'     => __('Le nom est requis', 'text-domain'),
    'email:required'        => __('L\'adresse email est requise', 'text-domain'),
    'email:email'           => __('L\'adresse email n\'est pas valide', 'text-domain'),
]);
```

Une fois l'action mise en place, il est nécessaire de créer un formulaire correspondant, voici un exemple d'utilisation :

```html

<?php if(get_post_notice()): ?>
    <span class="notice"><?php echo get_post_notice('message'); ?></span><br/>
<?php endif; ?>

<?php $data = get_post_data(); ?>
<form action="<?php echo get_post_action_url(); ?>" method="post">
    <input type="hidden" name="action" value="send_data">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('send_data'); ?>">
    <input type="text" name="firstname" placeholder="firstname" value="<?php echo (isset($data['firstname'])) ? $data['firstname'] : ''; ?>">
    <?php if(get_post_error('firstname')): ?>
        <span class="error"><?php echo get_post_error('firstname'); ?></span>
    <?php endif; ?>
    
    <input type="text" name="lastname" placeholder="lastname" value="<?php echo (isset($data['lastname'])) ? $data['lastname'] : ''; ?>">
    <?php if(get_post_error('lastname')): ?>
        <span class="error"><?php echo get_post_error('lastname'); ?></span>
    <?php endif; ?>
    
    <input type="email" name="email" placeholder="email" value="<?php echo (isset($data['email'])) ? $data['email'] : ''; ?>">
    <?php if(get_post_error('email')): ?>
        <span class="error"><?php echo get_post_error('email'); ?></span>
    <?php endif; ?>
    
    <button>Soumettre</button>
</form>
```

Plusieurs fonctions sont disponibles pour gérer les différentes informations, comme les erreurs ou bien le message renvoyé à l'utilisateur après la soumission du formulaire, voici la liste des fonctions et ce qu'elles font :

* `get_post_action_url()` : Permet de récupérer l'URL à utiliser pour envoyer les données, à utiliser dans l'action du formulaire, celui-ci doit également envoyer les données via la méthode POST.
* `get_post_errors()` : Permet de récupérer l'ensemble des erreurs du formulaire (sous forme de tableau PHP) suite à la vérification des données.
* `get_post_error($name)` : Permet de récupérer l'erreur spécifique d'un champ, celle-ci est retourné sous forme de chaine de caractères ou `null` s'il n'y a pas d'erreur. La valeur de `$name` correspond au `name` de l'input utilisé.
* `get_post_data()` : Permet de récupérer la totalité des champs soumis via le formulaire, afin de remettre les données dans celui-ci s'il y a des erreurs par exemple.
* `define_post_notice($type, $message)` : Permet de définir un message de succès ou d'erreur. La variable `$type` peut prendre n'importe quelle chaine de caractères mais par convention le mieux est d'utiliser : `success`, `error` ou `info`
* `get_post_notice()` : Permet de récupérer le message d'erreur sous forme de tableau avec le `type` et le `message`
* `get_post_notice($key)` : Permet de récupérer un élément spécifique de la notice la valeur de `$key`, ne pourra alors qu'être `type` ou `message`

Dans le but de permettre l'ajout de règles de vérification, un hook a été mis en place et doit être ajouté au fichier `functions.php` de votre thème, il fonctionne de la manière suivante :
```php
add_filter('register_validation_rules', function ($validator){

    $validator->addValidator('unique', new UniqueRule($pdo));
    return $validator;

}, 10, 1);
```
Pour en savoir plus vis à vis de l'enregistrement d'une règle, rendez-vous sur la [documentation du package](https://github.com/rakit/validation#registeroverride-rule)
___

### Actions via Ajax

Dans le but de soumettre les données d'un formulaire en passant par une requête asynchrone (Ajax), il est possible d'utiliser du code spécifique en l'ajoutant au fichier `functions.php` de votre thème.

La méthode utilise 3 paramètres :
* Paramètre n°1 : Nom de l'action (sans caractères spéciaux, sans espaces en utilisant uniquement lettres, tirets et tirets bas)
* Paramètre n°2 : Est-ce que l'action doit utilisable uniquement par un utilisateur connecté ? (false : Disponible pour tout le monde, true : disponible uniquement pour les utilisateurs connectés)
* Paramètre n°3 : Fonction de callback permettant le traitement des données
* Paramètre n°4 : Règles de vérification des données, se référer à la [documentation](https://github.com/rakit/validation#usage) du package pour visualiser les différentes possibilités en terme de validation des données.
* Paramètre n°5 : Est-ce que la requête est exécutée sur l'administration uniquement ?

Voici un exemple d'utilisation :
```php
use COQPIT\Plugins\Core\Actions\Ajax;

Ajax::addAction('save_data', false, function ($data){

    // $data = les données soumises via le formulaire
    // Effectuez le traitement souhaité ici
    // Attention c'est de l'Ajax, vous devez donc renvoyer du JSON

    wp_send_json_success(['message' => __('les données ont bien été enregistrées', 'text-domain')]);

}, [
    'firstname' => 'required',
    'lastname'  => 'nullable',
    'email'     => 'required|email'
], [
    'firstname:required'    => __('Le prénom est requis', 'text-domain'),
    'lastname:required'     => __('Le nom est requis', 'text-domain'),
    'email:required'        => __('L\'adresse email est requise', 'text-domain'),
    'email:email'           => __('L\'adresse email n\'est pas valide', 'text-domain'),
], false);
```

Une fois l'action mise en place, vous pouvez alors utiliser JavaScript pour effectuer une requête Ajax, voici un exemple d'utilisation :

```javascript
import { ajaxRequest } from './utils'

ajaxRequest(appScriptAjax.ajaxUrl, appScriptAjax.nonce, 'save_data', {
    firstname: 'firstname',
    lastname: 'lastname',
    email: 'firstname.lastname@coqpit.fr'
}).then((response) => {
    console.log(response)
}).catch((response) => {
    console.log(response)
    // response.status === 403 : Erreur lors de la vérification du nonce
    // response.status === 422 : Erreur lors de la validation des données, récupération des erreurs via response.data.errors
})
```

Voici également le contenu du fichier `utils.js` :

```javascript
export const documentReady = (fn) => {
    if (document.readyState !== 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

export const ajaxRequest = (url, nonce, action = null, data = {}) => {
    return new Promise((resolve, reject) => {

        let request = new XMLHttpRequest();

        request.open('POST', url, true);

        request.onload = function() {
            if (this.status >= 200 && this.status < 400){
                const data = JSON.parse(this.response)
                data.status = this.status
                resolve(data)
            } else if(this.status === 403){
                const data = JSON.parse(this.response)
                data.status = this.status
                reject(data)
            } else if(this.status === 422){
                const data = JSON.parse(this.response)
                data.status = this.status
                reject(data)
            } else {
                reject(new Error('An error occurred, data response has failed'))
            }
        }

        request.onerror = function() {
            reject(new Error('An error occurred, data response has failed'))
        }

        const formData = new FormData();

        for (const [key, value] of Object.entries(data)) {
            formData.append(key, value)
        }

        formData.append('action', action)
        formData.append('nonce', nonce)

        request.send(formData);

    })
}
```

Dans le but de permettre l'ajout de règles de vérification, un hook a été mis en place et doit être ajouté au fichier `functions.php` de votre thème, il fonctionne de la manière suivante :
```php
add_filter('register_validation_rules', function ($validator){

    $validator->addValidator('unique', new UniqueRule($pdo));
    return $validator;

}, 10, 1);
```
Pour en savoir plus vis à vis de l'enregistrement d'une règle, rendez-vous sur la [documentation du package](https://github.com/rakit/validation#registeroverride-rule)


Dans le but de pouvoir gérer les paramètres de l'action de façon dynamique un hook a été mis en place, attention le hook s'exécute au chargement de la page. Pour que ce soit uniquement sur les requêtes AJAX il est nécessaire d'utiliser la fonction `wp_doing_ajax()` :
```php
add_filter('coqpit_ajax_action_data', function ($action){

    /*
    $action = [
        'name'      => $actionName,  // Nom de l'action (string)
        'callback'  => $callback, // Méthode à utiliser (fn)
        'rules'     => $validationRules, // Règles de validation (Array)
        'messages'  => $validationMessages, // Messages des règles de validation (Array)
        'admin'     => $adminAction // Admin/Pas Admin (bool)
    ];
    */

    return $action;

}, 10, 1);
```

___

### ACF Synchronisation

Lorsque ACF est activé dans les plugins, un répertoire nommé `acf` sera automatiquement créé à la racine du thème afin que les fichiers de synchronisation soient sauvegardés et récupérés automatiquement.

Un indicateur avec menu déroulant sera affiché dans la barre d'administration (uniquement sur la back-office) afin d'indiquer lorsqu'une synchronisation ACF est disponible.
___

### ACF Pages d'options

Les arguments à utiliser en paramètres sont les mêmes que ceux de la documentation officielle ACF :

* [Page d'option](https://www.advancedcustomfields.com/resources/acf_add_options_page/)
* [Sous page d'option](https://www.advancedcustomfields.com/resources/acf_add_options_sub_page/)

Voici un exemple d'utilisation :

```php
use COQPIT\Plugins\Core\CustomFields\OptionPage;

OptionPage::addPage([
    'page_title' => 'Layout'
]);

OptionPage::addSubPage([
    'page_title' => 'Footer',
    'parent_slug' => 'acf-options-layout'
]);

OptionPage::addSubPage([
    'page_title' => 'Header',
    'parent_slug' => 'acf-options-layout'
]);
```
___

### ACF Regles

Des règles ont été ajoutées à ACF pour plus de simplicité :

* Menu
  * Niveau du menu (Afin de pouvoir définir un champ sur un élément de menu et spécifiquement sur les éléments d'un certain niveau )

___

### Elementor Widgets

Les Widgets Elementor peuvent être automatiquement enregistrés, pour cela, plusieurs pré-requis :
* Elementor doit être installé
* Les Widgets doivent être enregistrés dans le dossier `{votre-theme}/elementor/widgets`
* Les Widgets doivent être enregistrés sous forme de classe n'ayant aucun namespace et respectant [l'exemple fourni par Elementor](https://developers.elementor.com/docs/widgets/widget-structure/) 

___

### WPML Multilingue

Une compatibilité avec WPML a été mise en place, permettant de prendre en compte les spécificités au niveau de la gestion des templates et des custom post types, lorsqu'une page est assignée à un type de contenu dans les options, les slugs seront automatiquement mis à jour si les autres langues existes.

Il est parfois possible que le comportement soit étrange, n'hésitez pas à ré-enregistrer les options de lecture ou recharger les permaliens, si le problème persiste [créez une issue sur le repo du plugin](https://git.coqpit.fr/plugins/wordpress/coqpit-core/-/issues).

___

### Optimisations

#### Images
Un système automatisé de génération de fichiers Webp lors de l'upload d'image a été mis en place afin d'optimiser la taille des fichiers, la récupération et l'affichage dans le thème se fait de manière automatique. Deux paramétres sont disponibles dans la section média des réglages permettant de définir la qualité de l'image créée et une autre permettant de définir si le site doit afficher les versions Webp ou non.

___

### Mode maintenance

Un mode maintenance est intégré au plugin. Les options sont à définir dans l'onglet "Personaliser" de WordPress puis dans la section "Maintenance". Vous pourrez activer/désactiver la page de maintenance au besoin.

* L'activation de la maintenance a pour effet d'utiliser le template de maintenance, toutes les pages (en front) du site sont redirigées vers la page d'accueil et la page d'accueil est réécrite pour afficher la maintenance.
* Pour modifier l'affichage de la page de maintenance il est nécessaire d'aller récupérer le template dans le plugin `{coqpit-core}/templates/maintenance.php` vous pourrez le mettre dans votre thème soit dans `{theme}/templates/maintenance.php` soit dans `{theme}/templates/page/maintenance.php` avec une priorisation pour ce dernier chemin.

___

### Emails

Pour simplifier le paramétrage de l'envoi des emails via WordPress une section "Emails" se trouve dans l'onglet "Réglages" permettant de définir le paramétrage SMTP à utiliser si nécessaire. Vous pouvez l'activer ou le désactiver à tout moment, il est inactif par défaut.
___

### Accès

Un rôle nommé "Gestionnaire" doit être défini comme rôle pour les comptes des clients, la gestion de l'affichage des éléments dans le back-office pour ce rôle est disponible dans l'onglet "Réglages" puis "Accès".
L'interface vous permettra d'afficher/masquer ce que voit le compte client. Certains menus sont désactivés par défaut, voici la liste :
* Tableau de bord : Mises à jour
* Apparence : Thèmes
* Apparence : Compositions
* Extensions (le menu et tous les sous-menus)
* Outils : Outils disponibles
* Outils : Importer
* Outils : Exporter
* Outils : Santé du site
* Réglages : Général
* Réglages : Écriture
* Réglages : Médias
* Réglages : Emails
* Réglages : Accès
* ACF (le menu et tous les sous-menus)

Pour ces pages par défaut un système de redirection de l'utilisateur a été mis en place dans le cas où l'URL est accédée directement depuis la barre d'adresse du navigateur.
Certains plugins utilisent des routes non conventionnelles empêchant le matching entre l'URL courante et celle "masquée". Ce qui par conséquent n'empêche pas l'accès via URL directe.

Dans le cas où vous souhaiteriez ouvrir l'un des accès par défaut ou bien vérrouiller obligatoirement un accès, il sera nécessaire de mettre en place des traitements particuliers via des hooks :

```php
add_filter('coqpit_access_locked_menus', function($menus) {
  // Ne concerne que les menus de premier niveau
  
  // Supprimer les éléments du tableau pour lesquels vous souhaitez que l'utilisateur ait accès
  $index = array_search('tools.php', $menus);
  unset($menus[$index]);
  
  // Ajouter des éléments dans le tableau pour lesquels vous souhaitez que l'accès soit vérrouillé
  $menus[] = 'tools.php';
  
  return $menus;
})

add_filter('coqpit_access_locked_submenus', function($submenus) {
  // Ne concerne que les menus de second niveau
  
  // Supprimer les éléments du tableau pour lesquels vous souhaitez que l'utilisateur ait accès
  $index = array_search('tools.php|site-health.php', $submenus);
  unset($submenus[$index]);
  
  // Ajouter des éléments dans le tableau pour lesquels vous souhaitez que l'accès soit vérrouillé
  $submenus[] = 'tools.php|site-health.php';
  
  // Attention à bien vérifier la manière d'écrire
  return $submenus;
})
```
Attention, certains accès sont également vérrouillé via les permissions (capabilities) de WordPress, dans certains cas, l'ajout d'un hook ayant pour but de remettre ou d'ajouter une permission sera nécessaire :

```php
add_action('admin_init', function () {
global $wp_roles;

$customer = $wp_roles->get_role('coqpit_admin');
$customer->add_cap('manage_tools');
}, 999);
```

Des permissions supplémentaires ont été créées pour gérer les affichages, voici la liste :

* `add_administrator_users` = Permet d'ajouter des comptes administrateur
* `edit_medias_settings` = Modifier les paramètres des médias
* `edit_emails_settings` = Modifier les paramètres SMTP
* `edit_writing_settings` = Modifier les paramètres d'écriture
* `edit_general_settings` = Modifier les paramètres généraux
* `manage_tools` = Accéder aux outils WordPress
* `manage_acf` = Accéder aux écrans d'édition ACF
* `edit_access_settings` = Accéder à la gestion des accès dont fait référence cette partie

Le rôle "Gestionnaire" peut créer des comptes, mais ne peux pas créer des administrateurs, il ne peut également pas supprimer les administrateurs.

**Le rôle et les permissions sont créées à l'activation du plugin et sont supprimés à sa désactivation, par conséquent, si vous souhaitez recharger les permissions du rôle "Gestionnaire", vous devrez désactiver le plugin COQPIT Core puis le réactiver.**
___

### Plugin Starter

Un Plugin starter a été préparé pour vous simplifier la création des plugins, vous pourrez le télécharger et visualiser la documentation via ce lien : [Plugin Starter](https://git.coqpit.fr/plugins/wordpress/plugin-starter)

___

### Ameliorations et ajouts prevus

* Ajout d'une page d'options dans le Back-Office permettant de gérer certains paramètres utilisés dans le plugin
* Ajout d'une page dans le Back-Office permettant de générer le thème par défaut et de l'activer en un clic.

___

### RGPD

Une option a été ajoutée pour permettre l'activation de Tarte au citron JS. Une fois activé cela ajoute le bandeau de cookie sur le site. Attention il est nécessaire d'effectuer quelques actions pour que tout soit correctement paramétré.

* Se rendre sur [https://tarteaucitron.io/](https://tarteaucitron.io/)
* Se connecter avec le compte COQPIT
* Ajouter le site en copiant les paramètres de "wordpress.test" lorsque c'est demandé
* C'est en place :)