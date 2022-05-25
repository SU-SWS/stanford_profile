(function ($) {
  'use strict';
  Drupal.behaviors.stanfordProfileHelperSiteImprove = {
    attach: function () {
      const analyticsSrc = '//siteimproveanalytics.com/js/siteanalyze_80352.js';
      // If a script tag with the source has already been added, do nothing.
      if ($(`script[src*="${analyticsSrc}"]`).length === 0) {
        const $script = $('<script>')
          .attr('type', 'text/javascript')
          .attr('async', true)
          .attr('src', analyticsSrc);

        $script.insertBefore($('script').first());
      }
    }
  };

})(jQuery);
