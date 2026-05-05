# AGENTS

## Objectif
Aider un développeur fullstack PHP/Twig/SCSS qui maintient son propre framework modulaire. L'objectif est de rester autonome, propre, sécurisé, performant et maintenable.

## Commandes principales
- `npm run dev` : compilation de développement avec Vite
- `npm run watch` : compilation en watch
- `npm run build` : build de production
- `composer start` : serveur PHP intégré pour développement local
- `composer test` : exécution des tests PHPUnit
- `docker compose exec node npm run watch` : watch dans le container Node

## Architecture clé
- `public/index.php` : point d'entrée HTTP
- `config/bootstrap.php` : bootstrap de l'application, chargement des dépendances et création du kernel
- `core/AppKernel.php` : initialisation, bootstrap sécurisé, sessions, erreurs, router
- `core/Routing/Router.php` : résolution d'URI via cache/compilation, validation middleware, lancement du contrôleur
- `core/Container.php` : petit container DI auto-wiré + bindings manuels dans `config/services.php`
- `core/BaseController.php` : rendu de vue, mapping `current_page` pour les assets, helpers de contrôle, flash, redirection
- `core/View.php` : wrapper Twig, chargement de vues modulaire, fonctions `vite_asset`, `vite_asset_optional`, main `app.request`

## Conventions importantes
- Modules sous `src/Modules/{Module}/Interface/Http/Controllers`
- Vues Twig dans `src/Modules/{Module}/UI/Views`
- `BaseController::render()` convertit `Module/view.twig` en `module-view` pour les assets SCSS/JS
- `config/services.php` lie manuellement les services et les bindings PDO/QueryBuilder
- `AppConfig` lit `.env` et charge `config/constants.php` pour les chemins
- `public/build/manifest.json` est utilisé par Twig pour résoudre les assets générés
- Ne pas modifier `vendor/` ou `public/build` manuellement sauf pour des corrections de build spécifiques

## Sécurité et production
- Sessions sécurisées activées dans `AppKernel` (`httponly`, `strict`, `secure`, `samesite=Strict`)
- `AppKernel` désactive l'affichage d'erreurs en production et journalise les erreurs critiques
- Twig met en cache les vues sauf en mode `APP_DEBUG=true`
- Valider toujours les slugs et données entrantes avant utilisation dans SQL et templates

## Priorités pour l'assistant
- Proposer des améliorations de structure sans casser la logique de routing et du container
- Préserver la séparation entre le framework maison et les modules métier
- Prioriser la lisibilité, la maintenabilité, la sécurité et la conformité aux bonnes pratiques PHP modernes
- Donner des recommandations concrètes pour refactorer, nettoyer, sécuriser et optimiser

## Ce qu'il faut éviter
- Changer les conventions de nommage des modules et vues sans raison claire
- Supprimer des bindings du container sans vérifier l'impact sur l'instanciation automatique
- Modifier `public/index.php` ou `config/bootstrap.php` sans tester le bootstrap complet
- Suggérer des dépendances externes inutiles quand une solution interne simple existe

## Notes utiles
- `AppConfig::getEnv()` lit déjà `$_ENV` et `.env`
- `config/constants.php` est le seul endroit où les chemins globaux sont définis
- `core/View.php` compile un cache de routes vues dans `storage/cache/routesViews.php`
- `Router` exige que `config/middlewares.php` renvoie les classes middleware valides
