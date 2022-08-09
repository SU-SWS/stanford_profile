/**
 * Behavior Example that works with Webpack.
 *
 * @see: https://www.npmjs.com/package/drupal-behaviors-loader
 *
 * Webpack wraps everything in enclosures and hides the global variables from
 * scripts so special handling is needed.
 */

export default {

  // Attach Drupal Behavior.
  attach(context, settings) {
    (function ($) {
      $('figure .media-entity-wrapper.video', context).each((i, video) => {
        $(video).closest('figure').css('width', '100%');
      });
    })(jQuery);
  },

  // Detach Example.//
  detach() {
    // console.log("Detached.");
  }
};
