const Encore = require('@symfony/webpack-encore');
const fs = require('fs');
const path = require('path');
const pagesPath = path.resolve(__dirname, 'src/assets/scss/pages');

fs.readdirSync(pagesPath).forEach(module => {

    const modulePath = path.join(pagesPath, module);

    if (!fs.statSync(modulePath).isDirectory()) {
        return;
    }

    fs.readdirSync(modulePath).forEach(file => {

        if (!file.endsWith('.scss') || file.startsWith('_')) {
            return;
        }

        const entryName = `${module}-${file.replace('.scss', '')}`;

        Encore.addStyleEntry(
            entryName,
            path.join(modulePath, file)
        );

        console.log(`✅ Auto SCSS: ${entryName}`);
    });
});

Encore
    // Dossier de build (le CSS compilé ira dans public/build/)
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Entrée globale (CSS + JS)
    .addEntry('app', './src/assets/js/app.js')
    .addStyleEntry('global', './src/assets/scss/_main.scss')

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










































