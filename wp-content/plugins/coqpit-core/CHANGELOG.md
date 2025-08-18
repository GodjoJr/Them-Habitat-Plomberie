# COQPIT Core - CHANGELOG

* 31/10/2024 - Ajout de l'intégration automatisée de Tarte au citron
* 21/10/2024 - Ajout d'un thème utilisant Vite.js et de fonctions spécifiques dans le plugin pour remplacer la version avec Laravel Mix (qui va devenir obsolète).
* 02/08/2024 - Ajout d'un rôle "Gestionnaire" pour les clients et d'une page d'option permettant de gérer les accès de ce rôle spécifiquement (et de masquer les menus dans le back-office)
* 24/07/2024 - Ajout d'une option permettant de mettre le site en maintenance + ajout d'options textuelles + ajout d'un template spécifique à la page de maintenance dans les templates utilisables dans le thème.
* 23/07/2024 - Ajout d'une option permettant de définir l'environnement dans lequel se trouve le site (Local, Préproduction, Production)
* 23/07/2024 - Ajout d'un indicateur dans le Back-Office permettant de savoir si des synchronisations ACF doivent être exécutées.
* 19/07/2024 - Ajout d'une fonctionnalité permettant de définir les informations de configuration SMTP depuis un nouvel onglet de réglages "Emails" (Par défaut utilise le mailer d'origine de WordPress)
* 19/07/2024 - Ajout d'une fonctionnalité permettant de définir dans les options des commentaires si ces derniers sont activés ou non (désactivé par défaut)
* 19/07/2024 - Ajout des options par défaut en base de données pour la fonctionnalité de génération des fichiers Webp
* 18/07/2024 - Ajout de l'optimisation automatique des images au format Webp (désactivable dans les paramètres des médias)
* 17/04/2024 - Fix ordre de chargement des plugins sur les Environnements sous Windows
* 12/03/2024 - Ajout d'une vérification sur les types de contenus pour afficher ou non les options de lecture dans les réglages (page d'archive + nombre de posts à afficher)
* 11/03/2024 - Vérification du nom de domaine dans le but d'ajouter automatiquement une meta noindex empêchant les robots Google d'indexer les pages des sites en pré-production
* 04/01/2024 - Ajout de l'automatisation du paramètrage des permaliens et de l'email d'admin au moment de l'activation du plugin
* 04/01/2024 - Modification de l'affichage du dashboard WordPress en cachant toutes les metabox à l'exception de la nouvelle metabox COQPIT et de la metabox "dashboard_right_now"
* 04/01/2024 - Suppression de l'ajout automatique de la fonction `wp_localize_script` sur les scripts enregistrés avec le plugin et ajout d'une méthode statique permettant de définir quels scripts utilisent de l'ajax. (voir la documentation [Scripts et Styles](README.md#scripts-et-styles))
* 20/12/2023 - Ajout de règles de réécriture pour les permaliens des catégories et des étiquettes du blog dans le cas où la pagination est utilisée (générait une erreur 404 auparavant)
* 13/12/2023 - Ajout de la possibilité de désactiver la vérification du nonce pour les requêtes Ajax utilisées sur l'interface d'administration
* 11/12/2023 - Modification de l'ordre de chargement des plugins, Le COQPIT Core charge désormais après ACF Pro si le plugin est installé, permettant d'utiliser les fonctions ACF dans le Plugin Core ainsi que les plugins utilisant le Core.
* 05/12/2023 - Modification du système d'inclusion des scripts, ajout de la possibilité de définir les dépendances d'un script via le nom du script (voir la documentation [Scripts et Styles](README.md#scripts-et-styles))
