const Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore
    // Dossier de build (le CSS compilé ira dans public_html/build/)
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Entrée globale (CSS + JS)
    .addEntry('app', './src/assets/js/app.js')
    .addStyleEntry('global', './src/assets/scss/_main.scss')

    // New entries:
    .addStyleEntry('gallery-index', './src/assets/scss/pages/gallery/index.scss')
    .addStyleEntry('cgu-policy', './src/assets/scss/pages/cgu/policy.scss')
    // -- new-line-generate-by-make-module --

    // Entrées spécifiques aux pages =>
    // => Admin/*.scss :
    .addStyleEntry('admin-category-index', './src/assets/scss/pages/admin/category/index.scss')
    .addStyleEntry('admin-laboratory-index', './src/assets/scss/pages/admin/laboratory/index.scss')
    .addStyleEntry('admin-user-index', './src/assets/scss/pages/admin/user/index.scss')
    .addStyleEntry('admin-dashboard-index', './src/assets/scss/pages/admin/dashboard/index.scss')
    .addStyleEntry('admin-product-index', './src/assets/scss/pages/admin/product/index.scss')

    // => Product/*.scss :
    .addStyleEntry('product-detail', './src/assets/scss/pages/product/detail.scss')
    .addStyleEntry('product-list', './src/assets/scss/pages/product/list.scss')

    // => User/*.scss :
    .addStyleEntry('user-create', './src/assets/scss/pages/user/create.scss')

    // => *.scss :
    .addStyleEntry('about-index', './src/assets/scss/pages/about/index.scss')
    .addStyleEntry('auth-login', './src/assets/scss/pages/auth/login.scss')
    .addStyleEntry('cgu-index', './src/assets/scss/pages/cgu/index.scss')
    .addStyleEntry('contact-index', './src/assets/scss/pages/contact/index.scss')
    .addStyleEntry('home-index', './src/assets/scss/pages/home/index.scss')

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
        from: './src/assets/fonts',
        to: 'fonts/[path][name].[hash:8].[ext]'
    })

    // Copier les images
    .copyFiles({
        from: './src/assets/img',
        to: 'img/[path][name].[hash:8].[ext]'
    })

    // Copier les vidéos webm
    .copyFiles({
        from: './src/assets/webm',
        to: 'webm/[path][name].[hash:8].[ext]'
    })

    // Aliases pour imports plus propres
    .addAliases({
        '@scss': path.resolve(__dirname, 'src/assets/scss'),
        '@js': path.resolve(__dirname, 'src/assets/js'),
    })
    ;

module.exports = Encore.getWebpackConfig();










































