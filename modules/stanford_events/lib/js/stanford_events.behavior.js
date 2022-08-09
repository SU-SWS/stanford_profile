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
  attach: function (context, settings) {
    (function ($) {

      // Get today's date
      const today = new Date();

      // Update so today is all of the day
      const a = today.setHours(0, 0, 0, 0);
      const todayBegin = parseInt(a, 10) / 1000;

      // Location for label
      const eventTypeLabelLoc =
        $('div.section-editorial-content')
          .find('.su-event-type');

      const eventLabelLoc =
        $('div.section-editorial-content .node-stanford-event-title');

      // Location for text
      const eventTextLocation =
        $('div.node-stanford-event-su-event-date-time');

      // Looking for the date on the node page
      const eventDate =
        $('section.event').attr('data-end-date');

      // Build the labels and sentence
      // Has type at top
      const eventTypeLabel =
        $('<span class="su-event-label-past">' + Drupal.t('Past Event') +
          '<span class="divider">' + Drupal.t('|') + '</span></span>');

      // Has no type at top
      const eventLabel =
        $('<span class="su-event-label-past">' + Drupal.t('Past Event') +
          '</span></span>');

      const pastText =
        $('<div class="su-event-text-past">' +
          Drupal.t('This event has passed.') + '</div>');

      // Apply past label and text
      if (eventDate < todayBegin) {
        pastText.appendTo(eventTextLocation.last());

        if (eventTypeLabelLoc.length) {
          eventTypeLabel.prependTo(eventTypeLabelLoc.first());
        }
        else if (eventLabelLoc.length) {
          eventLabel.prependTo(eventLabelLoc);
        }
      }
    })(jQuery);
  }
};
