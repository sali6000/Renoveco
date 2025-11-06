#!/bin/bash
set -euo pipefail

# Inclure les fonctions communes
source "$(dirname "$0")/utils.sh"

# 🧹 Nettoyage des logs
log "🧹 Nettoyage des logs..."
rm -rf ./storage/logs/* || error "⚠️ Impossible de supprimer certains logs."

# 🧹 Nettoyage du dossier build
log "🧹 Nettoyage du dossier build"
rm -rf ./public/build/* || error "⚠️ Impossible de supprimer le dossier build"

log "✅ Étape 3 terminée"