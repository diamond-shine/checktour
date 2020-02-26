const path = require('path');

const basePath = __dirname;
const srcPath = path.resolve(__dirname, './src');
const storagePath = path.resolve(__dirname, '../../storage');

const outputPath = process.env.BUILD_MODE === 'server' ?
    path.resolve(__dirname, '../../storage/app/static') :
    path.resolve(__dirname, '../../static');

const fonts = {
    mask: /\/fonts\/.*\.(ttf|woff|woff2|eot|otf|svg)/,
    path: {
        src: path.resolve(srcPath, './fonts'),
        output: path.resolve(outputPath, './fonts')
    }
};

const images = {
    mask: /\/img\/.*\.(png|jpg|jpeg|gif|svg)/,
    path: {
        src: path.resolve(srcPath, './img'),
        output: path.resolve(outputPath, './img')
    },
    manifest: path.resolve(outputPath, './img/manifest.json')
};

const styles = {
    entries: [ '../src/scss/app.scss' ],
    watch: [
        '../src/scss/*.scss',
        '../src/scss/**/*.scss'
    ],
    baseUri: 'css',
    path: {
        output: path.resolve(outputPath, './css')
    },
    manifest: {
        path: path.resolve(storagePath, './assets/manifest/parts'),
        name: 'manifest-styles.json'
    }
};

const browserSync = {
    watch: [],
    config: {
        open: true,
        notify: true,
        server: {
            baseDir: [
                path.resolve(basePath, './server'),
                path.resolve(__dirname, '../../storage/app/static')
            ]
        }
    }
};

const scripts = {
    path: {
        entry: {
            app: '../src/js/index.js'
        },
        output: path.resolve(outputPath, './js')
    },
    manifest: {
        path: path.resolve(storagePath, './assets/manifest/parts'),
        name: 'manifest-scripts.json'
    }
};

module.exports = {
    path: {
        src: srcPath,
        output: outputPath,
        storage: storagePath
    },
    fonts,
    images,
    styles,
    scripts,
    browserSync,
    jsonServer: {
        port: 3005,
        db: path.resolve(basePath, './server/db'),
        routes: path.resolve(basePath, './server/routes.json')
    },
    manifest: {
        output: `${storagePath}/assets/manifest/manifest.json`,
        files: [
            `${styles.manifest.path}/${styles.manifest.name}`,
            `${scripts.manifest.path}/${scripts.manifest.name}`
        ]
    },
    dataServer: {
        path: path.resolve(srcPath, 'server')
    }
};
