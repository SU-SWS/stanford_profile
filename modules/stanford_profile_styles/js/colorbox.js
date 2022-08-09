(function ($, Drupal, once) {
  'use strict';
  Drupal.behaviors.stanfordColorbox = {
    attach: function (context, settings) {
      $(once('stanford-colorbox', 'a.colorbox', context)).append($('<span class="sr-only">Opens gallery dialog</span>'))
      $('#colorbox', context).attr('aria-label', 'Image gallery');
    }
  };

})(jQuery, Drupal, once);
