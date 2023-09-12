const config = require("./src/config");
const Webpack = require("webpack");
const AssetsWebpackPlugin = require('assets-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("css-minimizer-webpack-plugin");

var webpackConfig = {
  entry: {
    "admin":         ["/src/scss/admin/index.scss"],
    "base":          ["/src/js/base.js"],
    "behaviors":     ["/src/js/behaviors.js"],
    "ckeditor":      ["/src/scss/ckeditor.scss"],
    "ckeditor5":     ["/src/scss/ckeditor5.scss"],
    "components":    ["/src/scss/components/index.scss"],
    "layout":        ["/src/scss/layout/index.scss"],
    "print":         ["/src/scss/print/index.scss"],
    "search-page":   ["/src/scss/pages/search/index.scss"],
    "state":         ["/src/scss/state/index.scss"],
    "theme":         ["/src/scss/theme/index.scss"],
    "user_login":    ["/src/scss/admin/user_login.scss"],
    "content/policy":["/src/scss/content/policy/index.scss"],
  },
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