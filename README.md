🧠 💡 FLOW COMPLET SIMPLE À RETENIR
1. git pull origin main
2. git status
3. git add .
4. git commit -m "message"
5. git push origin main
6. (serveur) git pull / cPanel sync
7. deploy automatique (.cpanel.yml)





🧠 🔥 WORKFLOW GIT STANDARD (LOCAL → ONLINE)
🥇 1. TOUJOURS commencer par récupérer l’état du serveur
git pull origin main

👉 but :

éviter les conflits
récupérer les changements GitHub / serveur
🥈 2. Vérifier ton état local
git status

👉 tu dois comprendre :

fichiers modifiés
fichiers non suivis
🥉 3. Ajouter tes modifications
git add .

👉 prépare tous tes changements pour commit

🧾 4. Créer un commit (version snapshot)
git commit -m "description claire du changement"

👉 ex :

git commit -m "fix responsive layout form"
🚀 5. Envoyer vers GitHub (remote)
git push origin main

👉 ça met ton code en ligne (repo distant)

🌍 6. SUR LE SERVEUR (cPanel)

Selon ton setup :

🔄 Option A — manuel
git pull origin main
⚙️ Option B — automatique (cPanel Git)

👉 tu fais juste :

“Update from Remote”
ou webhook auto
🚀 7. Déploiement (si .cpanel.yml)

cPanel exécute automatiquement :

.cpanel.yml → tasks

Ex :

rsync /public → public_html