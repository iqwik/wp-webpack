const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const ReplaceInFileWebpackPlugin = require('replace-in-file-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

const localhost = 'http://novagric.loc';
const themePath = path.join(__dirname, 'wp-content/themes/iqwik');
const jsPath = path.join(themePath, '/assets/src/js/index.js');
const publicFolder = 'assets/public'
const prodPath = path.join(themePath, `/${publicFolder}/`);

module.exports = (env, argv) => {
    const isDev = argv.mode === 'development'

    const rules = [
        {
            test: /\.js$/,
            exclude: /node_modules/,
            loader: 'babel-loader',
        },
        {
            test: /\.(sa|sc|c)ss$/,
            exclude: /node_modules/,
            use: [
                { loader: MiniCssExtractPlugin.loader, options: { hmr: isDev } },
                { loader: 'css-loader' },
                'sass-loader',
                'postcss-loader',
                'resolve-url-loader',
            ]
        },
        {
            test: /\.(png|jpe?g|gif)$/,
            use: [
                {
                    loader: 'file-loader',
                    options: {
                        context: prodPath,
                        name: '[path][name].[ext]?v=[hash:16]',
                        publicPath: `./${publicFolder}/`
                    },
                },
                'img-loader'
            ]
        }
    ];

    let plugins = [
        new MiniCssExtractPlugin({ filename: '../../style.css' }),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery'
        }),
        new ReplaceInFileWebpackPlugin([{
            dir: themePath,
            files: ['version.php'],
            rules: [{
                search: new RegExp(/\$bundle_version\s=\s\'\d+\'/, 'gi'),
                replace: () => `$bundle_version = '${Number(new Date())}'`
            }]
        }]),
    ];

    if (isDev) {
        plugins = [...plugins, new BrowserSyncPlugin({
            files: '**/*.php',
            proxy: localhost
        })];
    }

    return {
        context: themePath,
        entry: { main: jsPath },
        output: {
            path: prodPath,
            filename: 'js/[name].js',
        },
        // devtool: isDev ? 'cheap-eval-source-map' : false,
        resolve: {
            modules: [__dirname, 'node_modules'],
            extensions: ['.js'],
        },
        module: { rules },
        plugins,
        optimization: { minimize: !isDev }
    }
}
