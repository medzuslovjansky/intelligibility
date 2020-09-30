var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // .enableSassLoader(function(sassOptions) {}, {
    //             resolveUrlLoader: false
    //  })
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('js/common', './assets/js/common.js')
    .addStyleEntry('css/app', './assets/css/global.scss')
    .addStyleEntry('css/admin', './assets/css/admin_global.scss')
    .copyFiles({from: './assets/img'})
    // uncomment if you use Sass/SCSS files
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
