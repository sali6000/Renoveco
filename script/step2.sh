#!/bin/bash
set -euo pipefail

# Inclure les fonctions communes
source "$(dirname "$0")/utils.sh"

# 🔁 Liste des fichiers dans lesquels on remplace '--monsite'
FILES_TO_UPDATE=(
  "./docker-compose.yaml"
)

# ✅ Vérifie la présence de tous les fichiers, boucle si un est manquant
while true; do
  ALL_EXIST=true
  echo "📂 Fichiers à modifier :"
  for file in "${FILES_TO_UPDATE[@]}"; do
    if [[ ! -f "$file" ]]; then
      error "❌ Fichier manquant : $file"
      ALL_EXIST=false
    else
      echo "   ✅ $file"
    fi
  done

  if [[ "$ALL_EXIST" == true ]]; then
    read -p "❓ Confirmer le remplacement de '--monsite' dans ces fichiers ? (y/n) : " CONFIRM
    [[ "$CONFIRM" =~ ^[yY]$ ]] && break
  else
    error "🛠️ Corrige les fichiers manquants et réessaye."
    pause
  fi
done

# 🔁 Remplacement dans les fichiers
log "✏️ Remplacement de '--monsite' par '$PROJECT_NAME'..."
for file in "${FILES_TO_UPDATE[@]}"; do
  sed -i "s/--monsite/$PROJECT_NAME/g" "$file"
done

log "✅ Étape 2 terminée"