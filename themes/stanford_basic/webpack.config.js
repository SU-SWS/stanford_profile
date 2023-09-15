/* eslint-disable no-undef */
/**
 * Decanter 6 - Webpack Configuration
 */

// Requires / Dependencies
const path = require('path');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');
const TerserPlugin = require('terser-webpack-plugin');

// Paths
const npmPackage = 'node_modules';
const srcDir = path.resolve(__dirname, 'src');
const outputDir = path.resolve(__dirname, 'dist');
// process.env.NODE_ENV is NOT set, so use the name of the npm script as the clue.
const devMode = process.env.npm_lifecycle_event !== 'publish';

// Module Exports.
module.exports = {
  name: 's',
  // Define the entry points for which webpack builds a dependency graph.
  entry: {
    "admin":         "/src/scss/admin/index.scss",
    "base":          "/src/js/base.js",
    "behaviors":     "/src/js/behaviors.js",
    "ckeditor":      "/src/scss/ckeditor.scss",
    "ckeditor5":     "/src/scss/ckeditor5.scss",
    "components":    "/src/scss/components/index.scss",
    "layout":        "/src/scss/layout/index.scss",
    "print":         "/src/scss/print/index.scss",
    "search-page":   "/src/scss/pages/search/index.scss",
    "state":         "/src/scss/state/index.scss",
    "theme":         "/src/scss/theme/index.scss",
    "user_login":    "/src/scss/admin/user_login.scss",
    "content/policy":"/src/scss/content/policy/index.scss",
  },
  // Where should I output the assets.
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, outputDir + '/js'),
    clean: true,
    assetModuleFilename: '../assets/[name][ext]'
  },
  // Allows for map files.
  devtool: 'source-map',
  resolve: {
    alias: {
      './@fortawesome': path.resolve(__dirname, npmPackage, '@fortawesome'),
      'basic-assets': path.resolve(__dirname, 'src/assets'),
      'decanter-assets': path.resolve(__dirname, npmPackage, 'decanter/core/src/img'),
      'fa-fonts': path.resolve(__dirname, npmPackage, '@fortawesome/fontawesome-free/webfonts')
    }
  },
  // Optimizations that are triggered by production mode.
  optimization: {
    moduleIds: 'deterministic',
    minimize: !devMode,
    minimizer: [
      new CssMinimizerPlugin(),
      new TerserPlugin()
    ]
  },
  plugins: [
    // A webpack plugin to manage files before or after the build.
    // Used here to:
    // - clean all generated files (js AND css) prior to building
    // - copy generated files to the styleguide after building
    // Copying to the styleguide must happen in this build because the 2 configs
    // run asynchronously, and the kss build finishes before this build generates
    // the assets that need to be copied.
    // https://www.npmjs.com/package/filemanager-webpack-plugin
    new FileManagerPlugin({
      events: {
        onStart: {
          delete: [outputDir + '/**/*']
        },
        onEnd: {
          copy: [
            {
              source: npmPackage + "/decanter/core/src/templates/**/*.twig",
              destination: outputDir + "/templates/decanter/"
            },
          ]
        }
      }
    }),
    // This plugin extracts CSS into separate files. It creates a CSS file per
    // JS file which contains CSS. It supports On-Demand-Loading of CSS and
    // SourceMaps.
    // https://github.com/webpack-contrib/mini-css-extract-plugin
    new MiniCssExtractPlugin({
      // Options similar to the same options in webpackOptions.output
      // both options are optional
      filename: '../css/[name].css',
      chunkFilename: '../css/[id].css'
    }),
    // This Webpack plugin will generate a JSON file that matches the original
    // filename with the hashed version.
    // https://github.com/webdeveric/webpack-assets-manifest
    new WebpackAssetsManifest({
      output: 'assets.json'
    })
  ],
  module: {
    rules: [
      // Apply babel ES6 compilation to JavaScript files.
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      },
      // Apply Plugins to SCSS/SASS files.
      {
        test: /\.s[ac]ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          // CSS Loader. Generate sourceMaps.
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
              url: true
            }
          },
          // Post CSS. Run autoprefixer plugin.
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true
            }
          },
          // SASS Loader. Add compile paths to include bourbon.
          {
            loader: 'sass-loader',
            options: {
              sassOptions: {
                sourceMap: true,
                lineNumbers: true,
                outputStyle: 'nested',
                precision: 10,
                includePaths: [
                  path.resolve(__dirname, npmPackage, 'bourbon/core'),
                  path.resolve(__dirname, srcDir, 'scss'),
                  path.resolve(__dirname, npmPackage)
                ]
              }
            }
          }
        ]
      },
      {
        test: /\.css$/i,
        use: [MiniCssExtractPlugin.loader, 'css-loader']
      }
    ]
  }
};