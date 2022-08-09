/**
 * Webpack Configuration File
 * @type {[type]}
 */

// /////////////////////////////////////////////////////////////////////////////
// Requires / Dependencies /////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

const path =                      require('path');
const webpack =                   require('webpack');
const autoprefixer =              require('autoprefixer');
const FileManagerPlugin =         require('filemanager-webpack-plugin');
const UglifyJsPlugin =            require("uglifyjs-webpack-plugin");
const MiniCssExtractPlugin =      require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin =   require("optimize-css-assets-webpack-plugin");
const WebpackAssetsManifest =     require("webpack-assets-manifest");
const ExtraWatchWebpackPlugin =   require("extra-watch-webpack-plugin");
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");
const CopyWebpackPlugin =         require('copy-webpack-plugin');

// /////////////////////////////////////////////////////////////////////////////
// Paths ///////////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

const npmPackage =  path.resolve(__dirname, "node_modules");
const srcDir =      path.resolve(__dirname, "lib");
const distDir =     path.resolve(__dirname, "dist");
const srcSass =     path.resolve(__dirname, "lib/scss");
const distSass =    path.resolve(__dirname, "dist/css");
const srcJS =       path.resolve(__dirname, "lib/js");
const distJS =      path.resolve(__dirname, "dist/js");
const srcAssets =   path.resolve(__dirname, "lib/assets");
const distAssets =  path.resolve(__dirname, "dist/assets");

// /////////////////////////////////////////////////////////////////////////////
// Functions ///////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

// /////////////////////////////////////////////////////////////////////////////
// Config //////////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

// Start configuring webpack.
var webpackConfig = {
  // What am i?
  name: 'stanford_basic',
  // Allows for map files.
  devtool: 'source-map',
  // What build?
  entry: {
    "news-node.behaviors":  path.resolve(srcJS,   "news-node.behaviors.js"),
    "news-node":            path.resolve(srcSass, "components/news-node/index.scss"),
    "news-list-item":       path.resolve(srcSass, "components/news-list-item/index.scss"),
    "news-list.behaviors":  path.resolve(srcJS,   "news-list.behaviors.js"),
    "newsletter":           path.resolve(srcSass, "components/newsletter/index.scss"),
    "news-vertical-teaser": path.resolve(srcSass, "components/news-vertical-teaser/index.scss")
  },
  // Where put build?
  output: {
    filename: "[name].js",
    path: distJS
  },
  // Additional module rules.
  module: {
    rules: [
      // Drupal behaviors need special handling with webpack.
      // https://www.npmjs.com/package/drupal-behaviors-loader
      {
        test: /\.behavior.js$/,
        exclude: /node_modules/,
        options: {
          enableHmr: true
        },
        loader: 'drupal-behaviors-loader'
      },
      // Good ol' Babel.
      {
        test: /\.js$/,
        loader: 'babel-loader',
        query: {
          presets: ['@babel/preset-env']
        }
      },
      // Apply Plugins to SCSS/SASS files.
      {
        test: /\.s[ac]ss$/,
        use: [
          // Extract loader.
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
              sourceMap: true,
              plugins: () => [
                autoprefixer( {grid: true} )
              ]
            }
          },
          // SASS Loader. Add compile paths to include bourbon.
          {
            loader: 'sass-loader',
            options: {
              includePaths: [
                npmPackage
              ],
              sourceMap: true,
              lineNumbers: true,
              outputStyle: 'nested',
              precision: 10
            }
          }
        ]
      },
      // Apply plugin to font assets.
      {
        test: /\.(woff2?|ttf|otf|eot)$/,
        loader: 'file-loader',
        options: {
          name: "[name].[ext]",
          publicPath: "../assets/fonts",
          outputPath: "../assets/fonts"
        }
      },
      // Apply plugins to image assets.
      {
        test: /\.(png|jpg|gif)$/i,
        use: [
          // A loader for webpack which transforms files into base64 URIs.
          // https://github.com/webpack-contrib/url-loader
          {
            loader: "file-loader",
            options: {
              name: "[name].[ext]",
              publicPath: "../assets/img",
              outputPath: "../assets/img"
            }
          }
        ]
      },
      // Apply plugins to svg assets.
      {
        test: /\.(svg)$/i,
        use: [
          {
            loader: "file-loader",
            options: {
              name: "[name].[ext]",
              publicPath: "../assets/svg",
              outputPath: "../assets/svg"
            }
          }
        ]
      },
    ]
  },
  // Build optimizations.
  optimization: {
    // Uglify the Javascript & and CSS.
    minimizer: [
      // Shrink JS.
      new UglifyJsPlugin({
        cache: true,
        parallel: true,
        sourceMap: true
      }),
      // Shrink CSS.
      new OptimizeCSSAssetsPlugin({})
    ],
  },
  // Plugin configuration.
  plugins: [
    // Remove JS files from render.
    new FixStyleOnlyEntriesPlugin(),
    // Output css files.
    new MiniCssExtractPlugin({
      filename:  "../css/[name].css"
    }),
    // A webpack plugin to manage files before or after the build.
    // Used here to:
    // - clean all generated files (js AND css) prior to building
    // - copy generated files to the styleguide after building
    // Copying to the styleguide must happen in this build because the 2 configs
    // run asynchronously, and the kss build finishes before this build generates
    // the assets that need to be copied.
    // https://www.npmjs.com/package/filemanager-webpack-plugin
    new FileManagerPlugin({
      onStart: {
        delete: [distDir]
      }
    }),
    // Add a plugin to watch other files other than that required by webpack.
    // https://www.npmjs.com/package/filewatcher-webpack-plugin
    new ExtraWatchWebpackPlugin( {
      files: [
        srcDir + '/**/*.twig',
        srcDir + '/**/*.json'
      ]
    }),
    // Manually copying any assets in lib/assets/svg located in js files
    new CopyWebpackPlugin([
      {
        from: 'lib/assets/svg',
        to : '../assets/svg'
      }
    ]),
  ]
};

// Add the configuration.
module.exports = [ webpackConfig ];
