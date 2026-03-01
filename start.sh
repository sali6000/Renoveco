#!/bin/bash
set -euo pipefail

log() { echo -e "\033[1;36m$1\033[0m"; }
error() { echo -e "\033[1;31m$1\033[0m"; }
pause() { read -p "⏸️ Appuyez sur une touche pour continuer..." -n1 -s; echo; }

# Définition de la variable
PROJECT_NAME=$(basename "$PWD") # -> ProjectV2.6

# 1️⃣ Tout en minuscules + Supprimer caractères spéciaux
LOWER_NAME=$(echo "$PROJECT_NAME" | tr '[:upper:]' '[:lower:]')
DOCKER_NAME=$(echo "$LOWER_NAME" | tr -cd 'a-z0-9') # -> projectv26

export PROJECT_NAME  # 🔑 Export pour la rendre disponible dans les sous-scripts
export DOCKER_NAME  

# Menu pour choisir l'étape de départ
echo "🔹 Choisissez l'étape de départ :"
echo "1) Vérification Docker, clé, nom projet"
echo "2) Remplacement docker-compose et déchiffrement"
echo "3) Lancement Docker, nettoyage logs, build frontend, ouverture navigateur"
read -p "➡️ Entrez le numéro de l'étape : " START_AT


# Définir le dossier des scripts
SCRIPT_DIR="./script"

# Lancer les scripts dans l’ordre
if [[ "$START_AT" -le 1 ]]; then
    log "▶️  Étape 1"
    bash "$SCRIPT_DIR/step1.sh"
fi

if [[ "$START_AT" -le 2 ]]; then
    log "▶️  Étape 2"
    bash "$SCRIPT_DIR/step2.sh"
fi

if [[ "$START_AT" -le 3 ]]; then
    log "▶️  Étape 3"
    bash "$SCRIPT_DIR/step3.sh"
fi

if [[ "$START_AT" -le 4 ]]; then
    log "▶️  Étape 4"
    bash "$SCRIPT_DIR/step4.sh"
fi

if [[ "$START_AT" -le 5 ]]; then
    log "▶️  Étape 5"
    bash "$SCRIPT_DIR/step5.sh"
fi

log "🎉 Toutes les étapes terminées !"
pause
