const mix = require("laravel-mix");
const path = require("path");

mix.webpackConfig({
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
    module: {
        rules: [
            {
                test: /\.(mp3)$/,
                use: [
                    {
                        loader: "file-loader",
                        options: {
                            name: "[name].[ext]",
                            outputPath: "assets/audio/", // adjust this path as needed
                        },
                    },
                ],
            },
        ],
    },
});

const apps = ["authentication", "starter", "pos", "panel", "app"];

apps.forEach((app) => {
    mix.js(`resources/js/apps/${app}/main.js`, `public/js/${app}.js`).vue();
});

mix.postCss("resources/css/poshub-modern-ui.css", "public/css/poshub-modern-ui.css");
