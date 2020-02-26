const gulp = require('gulp');
const watch = require('gulp-watch');

const config = require('../config');

const scss = require('./tasks/scss');
const staticServer = require('./tasks/staticServer');
const bundler = require('./tasks/bundler');
const cleaner = require('./tasks/cleaner');
const jsonServer = require('./tasks/jsonServer');
const optimizeImages = require('./tasks/optimizeImages');

const manifestBuilder = require('./utils/manifestBuilder');

gulp.task(
    'json-server',
    jsonServer()
);

gulp.task(
    'clean:images',
    cleaner([
        config.images.path.output
    ])
);
gulp.task(
    'clean:fonts',
    cleaner([
        config.fonts.path.output
    ])
);

gulp.task(
    'static-server',
    staticServer()
);

gulp.task(
    'optimize-images',
    optimizeImages()
);

gulp.task(
    'scss',
    scss()
);

gulp.task(
    'scss:watch',
    scss(true)
);

gulp.task(
    'webpack',
    bundler()
);

gulp.task(
    'webpack:watch',
    bundler(true)
);

gulp.task(
    'build-manifest',
    () => manifestBuilder.build()
);

gulp.task(
    'build-manifest:watch',
    () => {
        watch(
            [ '*/**', '*/**/**' ],
            {
                cwd: config.path.storage
            },
            file => {
                if (config.manifest.files.includes(file.path)) {
                    manifestBuilder.debounceBuild();
                }
            }
        );
    }
);

/*
|--------------------------------------------------------------------------
| Pub tasks
|--------------------------------------------------------------------------
*/
gulp.task(
    'build',
    gulp.series(
        [
            'clean:images',
            'clean:fonts',
            'scss',
            'webpack',
            'build-manifest'
        ].filter(value => value)
    )
);

gulp.task(
    'watch',
    gulp.series([
        'clean:images',
        'clean:fonts',
        gulp.parallel([
            'scss:watch',
            'webpack:watch',
            'build-manifest:watch'
        ])
    ])
);

gulp.task(
    'serv',
    gulp.series([
        'clean:images',
        'clean:fonts',
        gulp.parallel([
            'scss:watch',
            'webpack:watch',
            'json-server',
            'static-server'
        ])
    ])
);
