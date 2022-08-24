(function ($) {
  'use strict';
  Drupal.behaviors.stanfordProfileHelperAjaxViews = {
    attach: function (context) {
      const $sections = $('.views-infinite-scroll-content-wrapper > div', context);
      if ($sections.length === 0) {
        return;
      }

      const headers = [];
      var $focused = $(':focus');

      $sections.each((i, child) => {
        $(child).children('h3').map((j, header) => {
          const $header = $(header);

          const existingHeader = headers.find((a, b) => {
            return a.text === $header.text().trim();
          }, $header.text().trim());

          if (existingHeader) {
            const $items = $header.siblings('ul').children();
            existingHeader.elem.siblings('ul').append($items.detach());
            $header.remove();
            return;
          }
          headers.push({text: $header.text().trim(), elem: $header})
        });
      })
      $focused.focus();
    }
  };

})(jQuery);
