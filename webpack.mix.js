const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/init.js", "public/js")
   .vue({ version: 2 })  // Especificar la versión 2 de Vue
   .styles([
        'resources/assets/template/css/style.min.css'
        // "resources/js/assets/fonts/line-awesome/css/line-awesome.min.css",
        // "resources/js/assets/fonts/feather/style.min.css",
        // "resources/js/assets/vendors/css/vendors.min.css",
        // // 'resources/js/assets/vendors/css/forms/selects/selectivity-full.min.css',
        // // 'resources/js/assets/vendors/css/forms/icheck/icheck.css',
        // // 'resources/js/assets/vendors/css/forms/icheck/custom.css',
        // // 'resources/js/assets/vendors/css/weather-icons/climacons.min.css',
        // "resources/js/assets/vendors/css/charts/morris.css",
        // "resources/js/assets/vendors/css/charts/chartist.css",
        // "resources/js/assets/vendors/css/charts/chartist-plugin-tooltip.css",
        // "resources/js/assets/css/bootstrap.min.css",
        // "resources/js/assets/css/bootstrap-extended.min.css",
        // "resources/js/assets/css/colors.min.css",
        // "resources/js/assets/css/components.min.css",
        // "resources/js/assets/css/core/menu/menu-types/horizontal-menu.min.css",
        // "resources/js/assets/css/core/colors/palette-gradient.min.css",
        // "resources/js/assets/css/core/colors/palette-switch.min.css",
        // "resources/js/assets/css/pages/user-feed.min.css",
        // "resources/js/assets/css/pages/timeline.min.css",
        // "resources/js/assets/css/pages/dashboard-ecommerce.min.css",
        // "resources/js/assets/css/plugins/forms/selectivity/selectivity.min.css",
        // // 'resources/js/assets/css/plugins/checkboxes-radios.min.css',
        // "resources/js/assets/vendors/js/sweetalert2/dist/sweetalert2.min.css",
        // "resources/js/assets/css/plugins/forms/switch.min.css",
        // "resources/js/assets/css/style.css",
    ], "public/css/all.css");

mix.minify("public/js/init.js");

// Si en algún momento necesitas incluir app.js, este es el código base
// mix.js('resources/js/app.js', 'public/js')
//     .vue({ version: 2 }) // Asegurar versión 2 de Vue aquí también
//     .sass('resources/sass/app.scss', 'public/css');
