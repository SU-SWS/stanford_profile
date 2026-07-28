(function (Drupal) {

  Drupal.behaviors.paragraphAccordion = {
    attach(context) {
      $(once('accordion', '[data-component-id="jemison:paragraph_accordion"]')).each(function () {
        const $button = $('button', this);
        const $contents = $('.accordion--contents', this);
        const uniqueId = $button.text()
          .toLowerCase()
          .trim()
          .replaceAll(/[^a-z0-9]/g, '-')

        $contents.addClass('hidden')
          .attr('id', `${uniqueId}-contents`)
          .attr('aria-labelledby', `${uniqueId}-button`)
          .attr('role', 'region');

        $button.attr('id', `${uniqueId}-button`)
          .attr('aria-controls', `${uniqueId}-contents`)
          .attr('aria-expanded', false)
          .click(function () {
            $contents.toggleClass('hidden');
            $button.attr('aria-expanded', $button.attr('aria-expanded') === 'false')
          });
      });
    },
  };

})(Drupal);
