import { defineConfig } from 'vite';
import fs from 'fs';
import path from 'path';
import { viteStaticCopy } from 'vite-plugin-static-copy';

// ── Auto-scan SCSS par module  ──
const scssEntries = {};
const pagesPath = path.resolve(__dirname, 'src/assets/scss/pages');

fs.readdirSync(pagesPath).forEach(module => {
    const modulePath = path.join(pagesPath, module);
    if (!fs.statSync(modulePath).isDirectory()) return;

    fs.readdirSync(modulePath).forEach(file => {
        if (!file.endsWith('.scss') || file.startsWith('_')) return;
        const entryName = `${module}-${file.replace('.scss', '')}`;
        scssEntries[entryName] = path.join(modulePath, file);
        console.log(`✅ SCSS trouvé: ${entryName}`);
    });
});
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
        hmr: { host: 'localhost', port: 5173, clientPort: 5173, },
        watch: {
            usePolling: true,      // ← fix WSL
            interval: 300,         // vérifie toutes les 300ms
        }
    },
    css: {
        preprocessorOptions: { scss: {} }
    },
}));