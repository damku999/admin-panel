const mix = require("laravel-mix");

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

// Global Sass options to suppress deprecation warnings
const sassOptions = {
    sassOptions: {
        quietDeps: true,
        verbose: false,
        silenceDeprecations: [
            "legacy-js-api",
            "import",
            "global-builtin",
            "color-functions",
            "mixed-decls",
        ],
    },
};

// Admin Portal Assets (Clean Bootstrap 5 only)
mix.js("resources/js/admin/admin-clean.js", "public/js/admin.js")
    .sass(
        "resources/sass/admin/admin-clean.scss",
        "public/css/admin.css",
        sassOptions
    )
    .options({
        processCssUrls: false,
        autoprefixer: {
            options: {
                browsers: ["last 6 versions"],
            },
        },
    });

// Customer Portal Assets
mix.js("resources/js/customer/customer.js", "public/js").sass(
    "resources/sass/customer/customer.scss",
    "public/css",
    sassOptions
);

// Shared Assets (Legacy compatibility)
mix.js("resources/js/app.js", "public/js").sass(
    "resources/sass/app.scss",
    "public/css",
    sassOptions
);

// Copy FontAwesome webfonts
mix.copyDirectory(
    "node_modules/@fortawesome/fontawesome-free/webfonts",
    "public/webfonts"
);

// Asset optimization for production
if (mix.inProduction()) {
    mix.version().options({
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

// Enhanced webpack config to suppress warnings and improve build output
mix.webpackConfig({
    stats: {
        children: true,
        warningsFilter: [
            /Deprecation/,
            /sass-loader/,
            /legacy-js-api/,
            /color-functions/,
            /global-builtin/,
        ],
    },
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    {
                        loader: "sass-loader",
                        options: {
                            sassOptions: {
                                quietDeps: true,
                                silenceDeprecations: [
                                    "legacy-js-api",
                                    "import",
                                    "global-builtin",
                                    "color-functions",
                                    "mixed-decls",
                                ],
                            },
                        },
                    },
                ],
            },
        ],
    },
});
