const webpackConfig = require('./webpack.config.base');
const prodMixin = require('./webpack.mixin.prod');

module.exports = prodMixin(webpackConfig);
