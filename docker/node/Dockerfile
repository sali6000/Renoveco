FROM node:20-alpine

WORKDIR /app

# Copier package.json + package-lock.json
COPY package*.json ./

# Installer toutes les dépendances dev + prod
RUN npm install --include=dev

COPY src/assets ./src/assets
COPY webpack.config.js ./

RUN npm run build

# Conteneur tourne en arrière-plan (optionnel si tu veux exec dedans)
CMD ["tail", "-f", "/dev/null"]
