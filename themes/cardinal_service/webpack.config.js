/**
 * Webpack Configuration File
 * @type {[type]}
 */

// /////////////////////////////////////////////////////////////////////////////
// Requires / Dependencies /////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

const path = require('path');
const webpack = require('webpack');
const autoprefixer = require('autoprefixer');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');
const ExtraWatchWebpackPlugin = require('extra-watch-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');

// /////////////////////////////////////////////////////////////////////////////
// Paths ///////////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

const npmPackage = 'node_modules/';
const srcDir = path.resolve(__dirname, 'lib');
const distDir = path.resolve(__dirname, 'dist');
const srcSass = path.resolve(__dirname, process.env.npm_package_config_srcSass);
const distSass = path.resolve(
	__dirname,
	process.env.npm_package_config_distSass
);
const srcJS = path.resolve(__dirname, process.env.npm_package_config_srcJS);
const distJS = path.resolve(__dirname, process.env.npm_package_config_distJS);

// /////////////////////////////////////////////////////////////////////////////
// Functions ///////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

// /////////////////////////////////////////////////////////////////////////////
// Config //////////////////////////////////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////

// Start configuring webpack.
var webpackConfig = {
	// What am i?
	name: 'cardinal_service',
	// Allows for map files.
	devtool: 'source-map',
	// What build?
	entry: {
		base: path.resolve(__dirname, srcSass + '/base.scss'),
		ckeditor: path.resolve(__dirname, srcSass + '/ckeditor.scss'),
	},
	// Where put build?
	output: {
		filename: '[name].js',
		path: path.resolve(__dirname, distJS),
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
					enableHmr: false,
				},
				loader: 'drupal-behaviors-loader',
			},
			// Good ol' Babel.
			{
				test: /\.js$/,
				loader: 'babel-loader',
				query: {
					presets: ['@babel/preset-env'],
				},
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
							url: true,
						},
					},
					// Post CSS. Run autoprefixer plugin.
					{
						loader: 'postcss-loader',
						options: {
							sourceMap: true,
							plugins: () => [autoprefixer()],
						},
					},
					// SASS Loader. Add compile paths to include bourbon.
					{
						loader: 'sass-loader',
						options: {
							includePaths: [path.resolve(__dirname, npmPackage)],
							sourceMap: true,
							lineNumbers: true,
							outputStyle: 'nested',
							precision: 10,
						},
					},
				],
			},
			// Apply plugins to image assets.
			{
				test: /\.(png|jpg|gif)$/i,
				use: [
					// A loader for webpack which transforms files into base64 URIs.
					// https://github.com/webpack-contrib/url-loader
					{
						loader: 'url-loader',
						options: {
							// Maximum size of a file in bytes. 8.192 Kilobtyes.
							limit: 8192,
							fallback: {
								loader: 'file-loader',
								options: {
									name: '[name].[ext]',
									publicPath: '../../assets/img',
									outputPath: '../../assets/img',
								},
							},
						},
					},
				],
			},
			// Apply plugins to svg assets.
			{
				test: /\.(svg)$/i,
				use: [
					// A loader for webpack which transforms files into base64 URIs.
					// https://github.com/webpack-contrib/url-loader
					{
						loader: 'url-loader',
						options: {
							// Maximum size of a file in bytes. 8.192 Kilobtyes.
							limit: 8192,
							fallback: {
								loader: 'file-loader',
								options: {
									name: '[name].[ext]',
									publicPath: '../../assets/svg',
									outputPath: '../../assets/svg',
								},
							},
						},
					},
				],
			},
		],
	},
	// Build optimizations.
	optimization: {
		// Uglify the Javascript & and CSS.
		minimizer: [
			// Shrink JS.
			new UglifyJsPlugin({
				cache: true,
				parallel: true,
				sourceMap: true,
			}),
			// Shrink CSS.
			new OptimizeCSSAssetsPlugin({}),
		],
	},
	// Plugin configuration.
	plugins: [
		// Remove JS files from render.
		new FixStyleOnlyEntriesPlugin(),
		// Output css files.
		new MiniCssExtractPlugin({
			filename: '../css/[name].css',
		}),
		// A webpack plugin to manage files before or after the build.
		// https://www.npmjs.com/package/filemanager-webpack-plugin
		new FileManagerPlugin({
			onStart: {
				delete: [distDir],
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
		new ExtraWatchWebpackPlugin({
			files: [srcDir + '/**/*.twig', srcDir + '/**/*.json'],
		}),
	],
};

// Add the configuration.
module.exports = [webpackConfig];
