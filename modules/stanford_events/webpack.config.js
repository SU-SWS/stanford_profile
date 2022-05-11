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

// /////////////////////////////////////////////////////////////////////////////
// Paths ///////////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

const npmPackage = path.resolve(__dirname, 'node_modules');
const srcDir = path.resolve(__dirname, "lib");
const distDir = path.resolve(__dirname, "dist");
const srcSass = path.resolve(srcDir, 'scss');
const distSass = path.resolve(distDir, 'css');
const srcJS = path.resolve(srcDir, 'js');
const distJS = path.resolve(distDir, 'js');
const seriesSrcSass = path.resolve(__dirname, "modules/stanford_events_series/lib/scss");

// /////////////////////////////////////////////////////////////////////////////
// Functions ///////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

// /////////////////////////////////////////////////////////////////////////////
// Config //////////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

// Start configuring webpack.
var webpackConfig = {
  // What am i?
  name: 'stanford_events',
  // Allows for map files.
  devtool: 'source-map',
  // What build?
  entry: {
    "stanford_events.node.behaviors": path.resolve(srcJS, "stanford_events.js"),
    "stanford_events.node": path.resolve(srcSass, "stanford_events.node.scss"),
    "stanford_events.views": path.resolve(srcSass, "stanford_events.views.scss"),
    "stanford_events.person-cta": path.resolve(srcSass, "components/person-cta/stanford_events.person-cta.scss"),
    "stanford_events.event-schedule": path.resolve(srcSass, "components/event-schedule/stanford_events.event-schedule.scss"),
    "stanford_events.event-filter-menu": path.resolve(srcSass, "components/event-filter-menu/stanford_events.event-filter-menu.scss"),
    "stanford_events.event-list": path.resolve(srcSass, "components/event-list/stanford_events.event-list.scss"),
    "stanford_events.event-card": path.resolve(srcSass, "components/event-card/stanford_events.event-card.scss"),
    // Event Series.
    "../../modules/stanford_events_series/dist/css/stanford_events_series.node": path.resolve(seriesSrcSass, "stanford_events_series.node.scss"),
    "../../modules/stanford_events_series/dist/css/stanford_events_series.views": path.resolve(seriesSrcSass, "stanford_events_series.views.scss")
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
          enableHmr: false
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
                autoprefixer({ grid: true })
              ]
            }
          },
          // SASS Loader. Add compile paths to include bourbon.
          {
            loader: 'sass-loader',
            options: {
              includePaths: [
                npmPackage,
                srcSass,
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
    // https://www.npmjs.com/package/filemanager-webpack-plugin
    new FileManagerPlugin({
      onStart: {
        delete: [distDir]
      },
      // onEnd: {
        // copy: [
          // {
          //   source: npmPackage + "/decanter/core/src/templates/**/*.twig",
          //   destination: distDir + "/templates/decanter/"
          // },
          // {
          //   source: srcDir + "/assets/**/*",
          //   destination: distDir + "/assets/"
          // }
        // ],
      // },
    }),
    // Add a plugin to watch other files other than that required by webpack.
    // https://www.npmjs.com/package/filewatcher-webpack-plugin
    new ExtraWatchWebpackPlugin( {
      files: [
        srcDir + '/**/*.twig',
        srcDir + '/**/*.json'
      ]
    }),
  ]
};

// Add the configuration.
module.exports = [ webpackConfig ];
