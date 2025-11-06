const Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore
    // Dossier de build (le CSS compilé ira dans public/build/)
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Entrée globale (CSS + JS)
    .addEntry('app', './app/assets/js/app.js')
    .addStyleEntry('global', './app/assets/scss/_main.scss')

    // Entrées spécifiques aux pages
    .addStyleEntry('about', './app/assets/scss/pages/about.scss')
    .addStyleEntry('admin', './app/assets/scss/pages/admin.scss')
    .addStyleEntry('home', './app/assets/scss/pages/home.scss')
    .addStyleEntry('cgu', './app/assets/scss/pages/cgu.scss')
    .addStyleEntry('product-list', './app/assets/scss/pages/product-list.scss')
    .addStyleEntry('product-detail', './app/assets/scss/pages/product-detail.scss')
    .addStyleEntry('auth-login', './app/assets/scss/pages/auth-login.scss')
    .addStyleEntry('contact', './app/assets/scss/pages/contact.scss')


    // Activer notifications système
    .enableBuildNotifications()

    // SCSS -> CSS
    .enableSassLoader()

    // PostCSS (autoprefixer, minification CSS en prod)
    .enablePostCssLoader()

    // Sépare automatiquement les chunks (vendors.js, runtime.js…)
    .splitEntryChunks()

    // Runtime unique pour de meilleures perfs
    .enableSingleRuntimeChunk()

    // Source maps utiles en dev
    .enableSourceMaps(!Encore.isProduction())

    // Nettoyer le build avant chaque compilation
    .cleanupOutputBeforeBuild()

    // Hashing des fichiers pour le cache busting (uniquement en prod)
    .enableVersioning(Encore.isProduction())

    // Copier les images
    .copyFiles({
        from: './app/assets/img',
        to: 'img/[path][name].[hash:8].[ext]'
    })

    // Copier les vidéos webm
    .copyFiles({
        from: './app/assets/webm',
        to: 'webm/[path][name].[hash:8].[ext]'
    })

    // Aliases pour imports plus propres
    .addAliases({
        '@scss': path.resolve(__dirname, 'app/assets/scss'),
        '@js': path.resolve(__dirname, 'app/assets/js'),
    })
    ;

module.exports = Encore.getWebpackConfig();
