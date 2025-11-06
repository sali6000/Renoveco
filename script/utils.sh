#!/bin/bash
log() { echo -e "\033[1;36m$1\033[0m"; }
error() { echo -e "\033[1;31m$1\033[0m"; }
pause() { read -p "⏸️ Appuyez sur une touche pour continuer..." -n1 -s; echo; }
