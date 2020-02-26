/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md.
 */

'use strict';

/* eslint-env node */

const CKEditorWebpackPlugin = require('@ckeditor/ckeditor5-dev-webpack-plugin');
const { bundler, styles } = require('@ckeditor/ckeditor5-dev-utils');
const UglifyJsWebpackPlugin = require('uglifyjs-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const webpack = require('webpack');
const path = require('path');

module.exports = {
    devtool: 'source-map',
    performance: {
        hints: false
    },

    entry: path.resolve(__dirname, 'src', 'ckeditor.js'),

    output: {
        // The name under which the editor will be exported.
        library: 'Ckeditor',

        path: path.resolve(__dirname, 'build'),
        filename: 'ckeditor.js',
        libraryTarget: 'umd',
        libraryExport: 'default'
    },

    // optimization: {
    //     minimizer: [
    //         new UglifyJsWebpackPlugin({
    //             sourceMap: true,
    //             uglifyOptions: {
    //                 output: {
    //                     // Preserve CKEditor 5 license comments.
    //                     comments: /^!/
    //                 }
    //             }
    //         })
    //     ]
    // },

    optimization: {
        minimizer: [
            new TerserPlugin({
                terserOptions: {
                    sourceMap: false,
                    safari10: true,
                    mangle: true
                }
            })
        ]
    },

    plugins: [
        new CKEditorWebpackPlugin({
            // UI language. Language codes follow the https://en.wikipedia.org/wiki/ISO_639-1 format.
            // When changing the built-in language, remember to also change it in the editor's configuration (src/ckeditor.js).
            language: 'uk',
            additionalLanguages: 'all'
        }),
        new webpack.BannerPlugin({
            banner: bundler.getLicenseBanner(),
            raw: true
        })
    ],

    module: {
        rules: [{
            test: /\.svg$/,
            use: ['raw-loader']
        },
        {
            test: /\.css$/,
            use: [{
                loader: 'style-loader',
                options: {
                    injectType: 'singletonStyleTag'
                }
            },
            {
                loader: 'postcss-loader',
                options: styles.getPostCssConfig({
                    themeImporter: {
                        themePath: require.resolve('@ckeditor/ckeditor5-theme-lark')
                    },
                    minify: true
                })
            }]
        }]
    }
};
