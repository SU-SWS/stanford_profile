/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};
/**
 * Behavior Example that works with Webpack.
 *
 * @see: https://www.npmjs.com/package/drupal-behaviors-loader
 *
 * Webpack wraps everything in enclosures and hides the global variables from
 * scripts so special handling is needed.
 */

/* unused harmony default export */ var __WEBPACK_DEFAULT_EXPORT__ = ({
  // Attach Drupal Behavior.
  attach: function attach(context, settings) {
    // console.log("Attached.");
    (function ($) {
      $('.su-lockup__wordmark').parents('.su-lockup__cell1').addClass('empty-logo');
    })(jQuery);
  },
  // Detach Example.
  detach: function detach() {
    // console.log("Detached.");
  }
});
/******/ })()
;