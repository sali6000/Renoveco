#!/bin/bash
set -euo pipefail

# Inclure les fonctions communes
source "$(dirname "$0")/utils.sh"

# 🔐 Déchiffrement de l’environnement
while true; do
  log "🔓 Déchiffrement de .env.local.enc..."
  if openssl enc -aes-256-cbc -pbkdf2 -d -in .env.local.enc -out .env.local -pass file:./secure/env.local.key; then
    log "✅ Déchiffrement réussi"
    break
  else
    error "❌ Échec du déchiffrement. Vérifie que la clé est correcte."
    pause
  fi
done

# 🚀 Lancement de docker-compose avec debug
while true; do
  log "🚀 Lancement de docker-compose avec --debug..."
  if docker-compose --env-file .env.local up -d --build; then
    log "✅ Docker Compose lancé avec succès"
    break
  else
    error "❌ Erreur lors du lancement de docker-compose."
    pause
  fi
done

# 🧹 Nettoyage de la clé de sécurité
while true; do
  log "🧹 Suppression des fichiers sensibles..."
  rm -f .env.local ./secure/env.local.key && break || {
    error "⚠️ Impossible de supprimer les fichiers. Réessai..."
    pause
  }
done

log "✅ Étape 4 terminée"