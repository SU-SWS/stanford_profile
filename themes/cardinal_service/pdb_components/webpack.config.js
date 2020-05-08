const path = require('path');
const glob = require('glob')
const HtmlWebPackPlugin = require("html-webpack-plugin");

const entries = glob.sync('./**/app.jsx').reduce((acc, path) => {
  const entry = path.replace('.jsx', '').replace('src','lib');
  acc[entry] = path
  return acc
}, {});

module.exports = {
  entry: entries,
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname),
  },
  devtool: 'source-map',
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader"
        }
      },
      {
        test: /\.html$/,
        use: [
          {
            loader: "html-loader"
          }
        ]
      }
    ]
  },
  resolve: {
    extensions: ['.js', '.jsx'],
    modules: [path.resolve(__dirname, 'src'), 'node_modules']
  },
  plugins: [
    new HtmlWebPackPlugin({
      template: "./index.html",
      filename: "./node_modules/dev/index.html"
    })
  ]
};
