#!/usr/bin/env bash
set -e

# Définition de la variable
PROJECT_NAME=$(basename "$PWD") # -> ProjectV2.6

# 1️⃣ Tout en minuscules + Supprimer caractères spéciaux
LOWER_NAME=$(echo "$PROJECT_NAME" | tr '[:upper:]' '[:lower:]')
DOCKER_NAME=$(echo "$LOWER_NAME" | tr -cd 'a-z0-9') # -> projectv26

# 🔧 CONFIG
PHP_CONTAINER="$DOCKER_NAME-php-1"          # Nom du conteneur PHP (docker-compose ps pour vérifier)
NODE_CONTAINER="$DOCKER_NAME-node-1"      # Nom du conteneur Node (docker-compose ps pour vérifier)
APP_PATH="/var/www/html"     # Chemin de ton app dans le conteneur
DIST_DIR="./dist"            # Dossier où on va générer les fichiers de déploiement

echo "🚀 [1/5] Nettoyage de $DIST_DIR"
rm -rf "$DIST_DIR"
mkdir -p "$DIST_DIR"

echo "📦 [2/5] Installation des dépendances PHP en mode prod"
docker exec $PHP_CONTAINER composer install --no-dev --optimize-autoloader

echo "🎨 [3/5] Build des assets front"
docker exec $NODE_CONTAINER npm ci
docker exec $NODE_CONTAINER npm run build

echo "📂 [4/5] Extraction des dossiers nécessaires"
# Copie /vendor et /public/build depuis le conteneur vers ton hôte
mkdir -p "$DIST_DIR/public"

# Copie le reste de ton code source (hors vendor/node_modules)
echo "📂 [5/5] Extraction complète depuis le conteneur"
# Copie uniquement ce qui est nécessaire
docker cp $PHP_CONTAINER:$APP_PATH/app "$DIST_DIR/app"
docker cp $PHP_CONTAINER:$APP_PATH/config "$DIST_DIR/config"
docker cp $PHP_CONTAINER:$APP_PATH/core "$DIST_DIR/core"
docker cp $PHP_CONTAINER:$APP_PATH/secure "$DIST_DIR/secure"
docker cp $PHP_CONTAINER:$APP_PATH/storage "$DIST_DIR/storage"
docker cp $PHP_CONTAINER:$APP_PATH/vendor "$DIST_DIR/vendor"
docker cp $PHP_CONTAINER:$APP_PATH/composer.json "$DIST_DIR/composer.json"
docker cp $PHP_CONTAINER:$APP_PATH/composer.lock "$DIST_DIR/composer.lock"
docker cp $PHP_CONTAINER:$APP_PATH/.env "$DIST_DIR/.env"
docker cp $PHP_CONTAINER:$APP_PATH/.env.prod.enc "$DIST_DIR/.env.prod.enc"
docker cp $PHP_CONTAINER:$APP_PATH/public/index.php "$DIST_DIR/public/index.php"
docker cp $NODE_CONTAINER:/app/public/build "$DIST_DIR/public/build"

echo "✅ Build de production terminé !"
echo "Ton dossier $DIST_DIR est prêt à être déployé."