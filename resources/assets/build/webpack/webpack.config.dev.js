const webpackConfig = require('./webpack.config.base');
const devMixin = require('./webpack.mixin.dev');

module.exports = devMixin(webpackConfig);
