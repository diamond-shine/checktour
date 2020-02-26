
const gulp = require('gulp');
const fs = require('fs');
const path = require('path');
const sass = require('gulp-sass');
const postcss = require('gulp-postcss');
const sourceMaps = require('gulp-sourcemaps');
const rev = require('gulp-rev');
const revFormat = require('gulp-rev-format');
const sassImporter = require('node-sass-magic-importer');
const postCssPlugins = require('../../postcss.config').plugins;
const browserSync = require('../utils/browserSync');
const rename = require('gulp-rename');
const clean = require('gulp-clean');
const removeOld = require('../plugins/gulp-remove-old');

const config = require('../../config');

const build = () => () => {
    const stream = gulp.src(config.styles.entries)
        .pipe(
            sourceMaps.init()
        )
        .pipe(
            sass({
                importer: sassImporter({
                    cwd: path.resolve(__dirname, '../../')
                })
            }).on('error', sass.logError)
        )
        .pipe(
            postcss(postCssPlugins)
        );
        // .pipe(
        //     sourceMaps.write()
        // );

    if (process.env.NODE_ENV !== 'production') {
        stream.pipe(
            sourceMaps.write()
        );
    }

    if (process.env.BUILD_MODE !== 'server') {
        return stream
            .pipe(
                rename({
                    dirname: config.styles.baseUri
                })
            )
            .pipe(
                rev()
            )
            .pipe(
                revFormat({
                    prefix: '.'
                })
            )
            .pipe(
                gulp.dest(config.path.output)
            )
            .pipe(
                removeOld()
            )
            .pipe(
                rev.manifest({
                    path: config.styles.manifest.name
                })
            )
            .pipe(
                gulp.dest(config.styles.manifest.path)
            )
            .pipe(
                removeOld({
                    manifest: true,
                    staticPath: config.path.output
                })
            );
    }

    return stream
        .pipe(
            gulp.dest(config.styles.path.output)
        )
        .pipe(
            browserSync.stream()
        );
};

gulp.task('@scss:clean', () => {
    if (!fs.existsSync(config.styles.path.output)) {
        return new Promise(
            resolve => resolve()
        );
    }

    return gulp.src(config.styles.path.output, { read: false })
        .pipe(
            clean({ force: true })
        );
});

gulp.task(
    '@scss:build',
    build()
);

module.exports = (watch = false) => {
    if (!watch) {
        return gulp.series([
            '@scss:clean',
            '@scss:build'
        ]);
    }

    return gulp.series([
        '@scss:clean',
        '@scss:build',
        () => {
            gulp.watch(
                config.styles.watch,
                gulp.series([
                    '@scss:build'
                ])
            );
        }
    ]);
};
