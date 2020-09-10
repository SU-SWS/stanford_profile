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


      $(".opportunity-header-content-container", context).once('opportunity-tags-separator').each(function() {
        $('.node-su-opportunity-su-opp-dimension').siblings('.node-su-opportunity-su-opp-type').before('<span>, </span>');
        $('.node-su-opportunity-su-opp-commitment').siblings('.node-su-opportunity-su-opp-service-theme').before('<span> in </span>');
      });
    }
  };


})(jQuery, Drupal);

