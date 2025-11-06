#!/bin/bash
set -euo pipefail

# Inclure les fonctions communes
source "$(dirname "$0")/utils.sh"

log "🚀 Lancement de 'npm run build et composer install (autoload)' dans les conteneurs..."

# npm install : Installe toutes les dépendances nodeJS listées 
# dans dependencies et devDependencies (dont webpack) dans node_modules/ 
# 
# npm install <package> : installe un package spécifique et l’ajoute automatiquement dans package.json si tu veux.
#
# npm run <script> : Exécute les scripts définis dans la section "scripts" de package.json
docker exec -it "$DOCKER_NAME-node-1" npm run build || error "❌ Échec du build frontend"

# composer install : Installe toutes les dépendances PHP listées
# dans composer.lock < composer.json (voir: "require") dans /vendor
# (Si composer.lock est absent, composer.json crée le composer.lock)
docker exec -it "$DOCKER_NAME-php-1" composer install || error "❌ Échec de l'installation de composer"

# 🌐 Ouverture automatique du navigateur vers le projet
log "🌐 Ouverture du navigateur sur https://localhost..."

if command -v xdg-open >/dev/null; then
  # Linux
  xdg-open https://localhost
elif command -v open >/dev/null; then
  # macOS
  open https://localhost
elif command -v start >/dev/null; then
  # Windows (Git Bash / Cygwin)
  start https://localhost
else
  echo "⚠️ Impossible d'ouvrir le navigateur automatiquement. Va sur https://localhost"
fi

# 🎉 Message final
log "🎉 Installation terminée avec succès pour le projet : $PROJECT_NAME, vous êtes connecté en tant que 'superadmin' (DB)"