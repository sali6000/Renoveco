const Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore
    // Dossier de build (le CSS compilé ira dans public/build/)
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Entrée globale (CSS + JS)
    .addEntry('app', './app/assets/js/app.js')
    .addStyleEntry('global', './app/assets/scss/_main.scss')

    // Entrées spécifiques aux pages =>
    // => Admin/*.scss :
    .addStyleEntry('admin-category-index', './app/assets/scss/pages/admin/category/index.scss')
    .addStyleEntry('admin-laboratory-index', './app/assets/scss/pages/admin/laboratory/index.scss')
    .addStyleEntry('admin-user-index', './app/assets/scss/pages/admin/user/index.scss')
    .addStyleEntry('admin-dashboard-index', './app/assets/scss/pages/admin/dashboard/index.scss')
    .addStyleEntry('admin-product-index', './app/assets/scss/pages/admin/product/index.scss')

    // => Product/*.scss :
    .addStyleEntry('product-detail', './app/assets/scss/pages/product/detail.scss')
    .addStyleEntry('product-list', './app/assets/scss/pages/product/list.scss')

    // => User/*.scss :
    .addStyleEntry('user-create', './app/assets/scss/pages/user/create.scss')

    // => *.scss :
    .addStyleEntry('about-index', './app/assets/scss/pages/about.scss')
    .addStyleEntry('auth-login', './app/assets/scss/pages/auth-login.scss')
    .addStyleEntry('cgu-index', './app/assets/scss/pages/cgu.scss')
    .addStyleEntry('contact-index', './app/assets/scss/pages/contact.scss')
    .addStyleEntry('home', './app/assets/scss/pages/home.scss')

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

    // Copier les polices
    .copyFiles({
        from: './app/assets/fonts',
        to: 'fonts/[path][name].[hash:8].[ext]'
    })

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
