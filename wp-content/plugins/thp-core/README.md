# COQPIT Plugin Starter

## Installation

Pour installer le plugin, télécharger le via le lien suivant : [Télécharger le plugin](https://git.coqpit.fr/plugins/wordpress/plugin-starter/-/archive/main/plugin-starter-main.zip)

Déposez le plugin dans le dossier des plugins de votre WordPress : `wp-content/plugins/`, renommez le dossier du plugin par le nom que vous aurez défini.

### Renommer les fichiers

Il sera obligatoire de renommer certains fichiers et/ou valeurs au sein des fichiers du plugin, voici les fichiers concernés :

* `example-plugin.php` : doit être nommé par le même nom que le dossier du plugin
* `/assets/js/example-plugin.js` : doit être nommé par le même nom que le dossier du plugin
* `/assets/css/example-plugin.css` : doit être nommé par le même nom que le dossier du plugin
* `/resources/js/example-plugin.js` : doit être nommé par le même nom que le dossier du plugin
* `/resources/scss/example-plugin.scss` : doit être nommé par le même nom que le dossier du plugin
* `/src/Example.php` : Nom général (utilisé dans le fichier `example-plugin`, voir ci-dessous)

### Modification dans les fichiers

Comme pour le nom des fichiers, vous devrez mettre à jour les valeurs dans certains fichiers :

#### example-plugin.php

Voici les lignes qui doivent être modifiées

```php
<?php
/**
 * Plugin Name: COQPIT - Example plugin
 * Description: Starter de plugin pour COQPIT
 * Version: 1.0.0
 * Author: COQPIT - Agence Digitale
 * Author URI: https://www.coqpit.fr
 * Text Domain: coqpit-example-plugin
 * Requires at least: 6.2
 * Requires PHP: 8.1
 */

/*...*/

// Define necessary constants
define('COQPIT_EXAMPLE_PLUGIN_VERSION', '1.0.0');
define('COQPIT_EXAMPLE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('COQPIT_EXAMPLE_PLUGIN_URI', plugin_dir_url(__FILE__));
define('COQPIT_EXAMPLE_PLUGIN_PLUGIN_FILE', __FILE__ );

/*...*/

if(apply_filters('coqpit_core_loaded', null)){
    \COQPIT\Plugins\Example\Example::init();
}
```

#### package.json

Voici les lignes qui doivent être modifiées

```json
{
  "name": "coqpit-example-plugin",
  "version": "1.0.0"
}
```

#### composer.json

Voici les lignes qui doivent être modifiées

```json
{
  "name": "coqpit/example-plugin",
  "description": "An example plugin",
  "extra": {
    "strauss": {
      "namespace_prefix": "COQPIT\\Plugins\\Example\\Vendor\\",
      "classmap_prefix": "COQPIT_Plugins_Example_Vendor",
      "constant_prefix": "CPEV_"
    }
  },
  "autoload": {
    "psr-4": {
      "COQPIT\\Plugins\\Example\\": "src/"
    }
  }
}
```

#### webpack.mix.json

Voici les lignes qui doivent être modifiées

```js
mix.js('resources/js/example-plugin.js', 'assets/js/example-plugin.js')
  .sass('resources/scss/example-plugin.scss', 'assets/css/example-plugin.css');
```

#### sass-forwards.cjs

Voici les lignes qui doivent être modifiées

```js
let excludeFiles = ['example-plugin.scss', 'files.scss', 'globals.scss']
```

Remplacez example-plugin.scss par le nom de votre fichier scss (celui censé porter le même nom que le dossier du plugin)

#### /src/Example.php

Mettre à jour le fichier avec les bonnes valeurs et les bons noms de fichiers

```php
<?php
namespace COQPIT\Plugins\Example;

use COQPIT\Plugins\Core\Admin\Scripts;

class Example {

    public static function init ()
    {

        // Register Admin scripts and styles
        Scripts::register('example-plugin', [
            'assets/css/example-plugin.css',
            'assets/js/example-plugin.js'
        ],COQPIT_EXAMPLE_PLUGIN_URI, COQPIT_EXAMPLE_PLUGIN_PATH, true);

    }

}
```

## Ajout de Packages Composer

Vous avez la possibilité d'ajouter des packages composer via la manière traditionnelle, pour se faire, vous devez vous rendre dans le dossier de votre plugin et utiliser la commande :

```shell
composer require nom/package
```

Les namespaces des packages seront automatiquement préfixés lors de l'installation (avec le préfixe défini dans votre fichier `composer.json`), veillez donc à bien utiliser la version préfixée du package pour éviter les conflits avec d'autres plugins. 