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

      $('.su-secondary-nav', context).once('secondary-nav').each(function () {
        const rightColumn = $(this).closest('.jumpstart-ui--two-column').children('.flex-lg-9-of-12');
        if (rightColumn.find('.node-stanford-page-title').length === 0) {
          $(this).css('margin-top', '0');
        }
      });
    }
  };


})(jQuery, Drupal);

