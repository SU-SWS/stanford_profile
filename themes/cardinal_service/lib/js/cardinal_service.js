(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.HoverMenu = {
    attach: function (context, settings) {

      $(window).scroll(function () {
        if ($(window).scrollTop() > 5000) {
          $('.back-to-top', context).fadeIn();
          return;
        }

        $('.back-to-top', context).fadeOut();
      });

      $(".back-to-top", context).once('back-top').click(function (e) {
        e.preventDefault();
        $("html, body").animate({scrollTop: 0}, "slow");
      });

    }
  };
})(jQuery, Drupal);

