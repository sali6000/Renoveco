import { defineConfig } from 'vite';
import fs from 'fs';
import path from 'path';
import { viteStaticCopy } from 'vite-plugin-static-copy';

/**
 * Scan récursif d'un répertoire.
 * Retourne tous les fichiers .scss non-partiels (ne commençant pas par _).
 * Associe chaque fichier à une clé d'entrée construite depuis le chemin relatif.
 *
 * @param {string} baseDir  - Répertoire racine à scanner (ex: src/assets/scss/pages)
 * @param {string} currentDir - Répertoire courant (pour la récursion)
 * @param {Record<string, string>} entries - Accumulateur
 * @returns {Record<string, string>}
 */
function scanScssEntries(baseDir, currentDir = baseDir, entries = {}) {
    fs.readdirSync(currentDir).forEach(item => {
        const fullPath = path.join(currentDir, item);
        const stat = fs.statSync(fullPath);

        if (stat.isDirectory()) {
            // Récursion dans les sous-dossiers
            scanScssEntries(baseDir, fullPath, entries);
            return;
        }

        if (!item.endsWith('.scss') || item.startsWith('_')) return;

        // Chemin relatif depuis baseDir, sans extension
        // ex: "services/panneaux-photovoltaiques"
        const relativePath = path
            .relative(baseDir, fullPath)
            .replace(/\.scss$/, '');

        // Clé d'entrée : séparateurs "/" et "\" → "-"
        // ex: "services/panneaux-photovoltaiques" → "services-panneaux-photovoltaiques"
        const entryKey = relativePath.replace(/[\\/]/g, '-');

        entries[entryKey] = fullPath;
        console.log(`✅ SCSS trouvé : ${entryKey}  ←  ${path.relative(process.cwd(), fullPath)}`);
    });

    return entries;
}

const pagesPath = path.resolve(__dirname, 'src/assets/scss/pages');
const scssEntries = scanScssEntries(pagesPath);

export default defineConfig(({ command }) => ({
    publicDir: false,
    base: command === 'serve' ? '/' : '/build/',
    build: {
        outDir: './public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                app: './src/assets/js/app.js',
                main: './src/assets/scss/_main.scss',
                ...scssEntries,
            }
        }
    },
    plugins: [
        viteStaticCopy({
            targets: [
                {
                    src: 'src/assets/img',
                    dest: '.',
                    globOptions: { ignore: ['**/*Zone.Identifier'] }
                },
                {
                    src: 'src/assets/webm',
                    dest: '.',
                    globOptions: { ignore: ['**/*Zone.Identifier'] }
                },
            ]
        })
    ],
    resolve: {
        alias: {
            '@scss': path.resolve(__dirname, 'src/assets/scss'),
            '@js': path.resolve(__dirname, 'src/assets/js'),
        }
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
            port: 5173,
            clientPort: 5173,
        },
    },
    css: {
        preprocessorOptions: { scss: {} }
    },
}));