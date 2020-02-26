if (process.env.BUILD_MODE !== 'server') {
    require(`file-loader?name=./vue.[hash:10].js!../../node_modules/vue/dist/vue.js`);
    require(`file-loader?name=./vue-router.[hash:10].js!../../node_modules/vue-router/dist/vue-router.js`);
    require(`file-loader?name=./vuex.[hash:10].js!../../node_modules/vuex/dist/vuex.js`);
    require(`file-loader?name=./element-ui/index.[hash:10].js!../../node_modules/element-ui/lib/index.js`);
    require(`file-loader?name=./lodash.[hash:10].js!../../node_modules/lodash/lodash.js`);
    require(`file-loader?name=./ckeditor.[hash:10].js!../../node_modules/@ckeditor/ckeditor5/build/ckeditor.js`);
} else {
    require(`file-loader?name=./vue.js!../../node_modules/vue/dist/vue.js`);
    require(`file-loader?name=./vue-router.js!../../node_modules/vue-router/dist/vue-router.js`);
    require(`file-loader?name=./vuex.js!../../node_modules/vuex/dist/vuex.js`);
    require(`file-loader?name=./element-ui/index.js!../../node_modules/element-ui/lib/index.js`);
    require(`file-loader?name=./lodash.js!../../node_modules/lodash/lodash.js`);
    require(`file-loader?name=./ckeditor.js!../../node_modules/@ckeditor/ckeditor5/build/ckeditor.js`);
}
