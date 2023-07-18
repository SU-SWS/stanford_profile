const config = require("./lib/config");
const Webpack = require("webpack");
const AssetsWebpackPlugin = require('assets-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const glob = require('glob')
const path = require('path');

const componentStyles = glob.sync('./lib/components/**/*.scss').reduce((acc, path) => {
  const entry = path.replace('.scss', '').replace('./lib/', '');
  acc[entry] = path
  return acc
}, {});

const styleSheets = glob.sync('./lib/scss/*.scss').reduce((acc, file) => {
  if (file.indexOf('_') > 0) {
    return acc;
  }
  const entry =  path.basename(file).split('.').slice(0, -1).join('.');
  acc[entry] = file
  return acc
}, {});
const entryPoints = {...componentStyles, ...styleSheets }
console.log(entryPoints);
var webpackConfig = {
  entry: entryPoints,
  output: {
    path: config.distFolder,
    filename: '[name].js',
    publicPath: config.publicPath,
    clean: true
  },
  mode: config.isProd ? "production" : "development",
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /(node_modules)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        },
      },
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          config.isProd ? { loader: MiniCssExtractPlugin.loader } : 'style-loader',
          'css-loader',
          'postcss-loader',
          'sass-loader'
        ],
      }
    ]
  },
  plugins: [
    new AssetsWebpackPlugin({path: config.distFolder}),
    new MiniCssExtractPlugin({
      filename: '[name].css',
    }),
  ],
  optimization: {
    minimizer: [
      new OptimizeCSSAssetsPlugin(),
    ]
  }

};

if (config.hmrEnabled) {
  webpackConfig.plugins.push(new Webpack.HotModuleReplacementPlugin());
}
module.exports = webpackConfig;
