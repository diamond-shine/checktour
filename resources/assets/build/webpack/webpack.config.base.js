const path = require('path');
const webpack = require('webpack');
const lodash = require('lodash');

const FriendlyErrors = require('friendly-errors-webpack-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const ProgressPlugin = require('webpack/lib/ProgressPlugin');
const DynamicPublicPathPlugin = require('dynamic-public-path-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const CleanupPlugin = require('./plugins/cleanup');
const WebpackAssetsManifest = require('webpack-assets-manifest');
const ChunkProgressWebpackPlugin = require('chunk-progress-webpack-plugin');

const externals = require('../../externals');
const config = require('../../config');

const manifestReplacer = ({ key, value }) => {
    let finalKey = key;
    let finalValue = value;

    if (/\.js$/.test(key)) {
        finalKey = 'js/' + lodash.trimStart(key, '/');
        finalValue = 'js/' + lodash.trimStart(value, '/');
    } else if (/\.css/.test(key)) {
        finalKey = 'css/' + lodash.trimStart(key, '/');
        finalValue = value.replace('../../', '');
    }

    return {
        key: finalKey,
        value: finalValue
    };
};

const webpackConfig = {
    entry: config.scripts.path.entry,
    output: {
        path: config.scripts.path.output,

        publicPath: '__PUBLIC_PATH__'
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        js: [
                            {
                                loader: 'babel-loader',
                                options: require('../../babel.config')
                            }
                        ]
                    }
                }
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: [/node_modules/],
                options: require('../../babel.config')
            },
            {
                test: /\.svg$/,
                loader: 'svg-loader'
            }
        ]
    },
    externals,
    plugins: [
        new ProgressPlugin,
        new FriendlyErrors,
        new WebpackNotifierPlugin({
            alwaysNotify: true
        }),
        new DynamicPublicPathPlugin({
            externalGlobal: 'window.App.cdn',
            chunkName: 'app'
        }),
        new VueLoaderPlugin,
        new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
        new CleanupPlugin,
        new ChunkProgressWebpackPlugin
    ],
    resolve: {
        extensions: ['.js', '.json', '.vue'],
        modules: ['node_modules'],
        alias: {
            '@errors': path.resolve(__dirname, '../../src/js/errors'),
            '@store': path.resolve(__dirname, '../../src/js/store'),
            '@plugins': path.resolve(__dirname, '../../src/js/plugins'),
            '@components': path.resolve(__dirname, '../../src/js/components'),
            '@constants': path.resolve(__dirname, '../../src/js/constants'),
            '@routes': path.resolve(__dirname, '../../src/js/routes'),
            '@helpers': path.resolve(__dirname, '../../src/js/helpers'),
            '@modules': path.resolve(__dirname, '../../src/js/modules'),
            '@mixins': path.resolve(__dirname, '../../src/js/mixins'),
            '@vendor': path.resolve(__dirname, '../../src/js/vendor'),
            '@utils': path.resolve(__dirname, '../../src/js/utils'),
            '@router': path.resolve(__dirname, '../../src/js/router'),
            '@svg': path.resolve(__dirname, '../../static/src/svg'),
            '@base': path.resolve(__dirname, '../../../../'),
            '@src': path.resolve(__dirname, '../../src')
        }
    }
};

if (process.env.INSPECT_JS) {
    webpackConfig.plugins.push(
        new BundleAnalyzerPlugin
    );
}

if (process.env.BUILD_MODE === 'server') {
    webpackConfig.output.filename = '[name].js';
    webpackConfig.output.chunkFilename = '[name].js';
} else {
    webpackConfig.output.filename = '[name].[chunkhash:10].js';
    webpackConfig.output.chunkFilename = '[name].[chunkhash:10].js';

    webpackConfig.plugins.push(
        new WebpackAssetsManifest({
            output: path.relative(
                config.scripts.path.output,
                config.scripts.manifest.path
            ) + `/${config.scripts.manifest.name}`,
            customize: manifestReplacer,
            space: 2,
            writeToDisk: false,
            fileExtRegex: /\.\w{2,4}\.(?:map|gz)$|\.\w+$/i,
            sortManifest: true,
            merge: false,
            publicPath: ''
        })
    );
}

module.exports = webpackConfig;
