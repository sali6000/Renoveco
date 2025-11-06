#!/bin/bash
set -euo pipefail

log() { echo -e "\033[1;36m$1\033[0m"; }
error() { echo -e "\033[1;31m$1\033[0m"; }
pause() { read -p "⏸️ Appuyez sur une touche pour continuer..." -n1 -s; echo; }


# ✅ Vérifie que Docker est lancé (boucle tant que KO)
while true; do
  if docker info >/dev/null 2>&1; then
    log "🐳 Docker est lancé"
    break
  else
    error "🚫 Docker n'est pas lancé. Lance Docker Desktop d'abord."
    pause
  fi
done

RUNNING_CONTAINERS=$(docker ps -q)
if [[ -n "$RUNNING_CONTAINERS" ]]; then
  log "🛑 Arrêt de tous les conteneurs en cours..."
  docker stop $RUNNING_CONTAINERS
  log "✅ Tous les conteneurs ont été arrêtés."
else
  log "ℹ️ Aucun conteneur en cours d'exécution."
fi

# ✅ Vérifie la présence de la clé (boucle tant que manquante)
while true; do
  if [[ -f ./secure/env.local.key ]]; then
    log "🔑 Clé de chiffrement détectée."
    break
  else
    error "❌ La clé de sécurité owner ./secure/*.key est manquante."
    echo "💡 Veuillez la placer dans le dossier 'secure'."
    pause
  fi
done

log "✅ Étape 1 terminée"