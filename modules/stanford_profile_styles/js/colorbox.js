(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.stanfordColorbox = {
    attach: function (context, settings) {
      $('a.colorbox', context).once('stanford-colorbox').append($('<span class="sr-only">Opens gallery dialog</span>'))
      $('#colorbox', context).attr('aria-label', 'Image gallery');
    }
  };

})(jQuery, Drupal);
