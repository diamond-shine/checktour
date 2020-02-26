const webpack = require('webpack');

module.exports = (watch = false) => () => {
    const mode = process.env.NODE_ENV === 'production' ? 'prod' : 'dev';

    const config = require(`../webpack/webpack.config.${mode}`);

    config.watch = watch;

    return new Promise(
        resolve => webpack(config, (err, stats) => {
            if (err) {
                console.log('Webpack', err);
            }

            console.log(
                stats.toString({
                    colors: true
                })
            );

            resolve();
        })
    );
};
