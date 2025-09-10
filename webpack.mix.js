const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Modern Asset Management for Insurance Management System
 |--------------------------------------------------------------------------
 |
 | Optimized build configuration for dual-portal architecture:
 | - Admin Portal: Bootstrap 5 + SB Admin 2 compatibility
 | - Customer Portal: Bootstrap 5 + modern styling
 | - Shared: jQuery, Axios, FontAwesome
 |
 */

// Admin Portal Assets (Clean Bootstrap 5 only)
mix.js('resources/js/admin/admin-clean.js', 'public/js/admin.js')
    .sass('resources/sass/admin/admin-clean.scss', 'public/css/admin.css')
    .options({
        processCssUrls: false,
        autoprefixer: {
            options: {
                browsers: [
                    'last 6 versions',
                ]
            }
        }
    });

// Customer Portal Assets  
mix.js('resources/js/customer/customer.js', 'public/js')
    .sass('resources/sass/customer/customer.scss', 'public/css');

// Shared Assets (Legacy compatibility)
mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

// Copy FontAwesome webfonts
mix.copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts');

// Asset optimization for production
if (mix.inProduction()) {
    mix.version()
        .options({
            terser: {
                terserOptions: {
                    compress: {
                        drop_console: true,
                    },
                },
            },
        });
} else {
    mix.sourceMaps();
}

// Add webpack stats to see warnings
mix.webpackConfig({
    stats: {
        children: true,
    }
});
