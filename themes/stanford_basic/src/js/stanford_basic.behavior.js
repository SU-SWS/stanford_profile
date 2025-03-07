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
    (function ($, once) {

      // If some embed code contains a caption, make sure the figure respects
      // the iframe width of 100%.
      $('figure', context).each(function() {
        const $iframeWithin = $('iframe', this);
        const iframeWidth = $iframeWithin.attr('width');
        if ($iframeWithin.length && (!iframeWidth || iframeWidth === '100%')) {
          $(this).css('width', '100%');
        }
      })

      // Validate there is a skip link anchor for the main content. If not,
      // default to #page-content.
      const $title = $('h1', context);
      if ($title.length) {
        if (!$title.attr('id')) {
          $title.attr('id', 'page-title');
        }
        $('.su-masthead .su-skipnav--content', context).attr('href', '#' + $title.attr('id'));
      } else {
        if (!$('#main-content', context).length) {
          $('.su-skipnav--content', context).attr('href', '#page-content');
        }
      }

      // Validate there is a skip link for the secondary navigation. If not,
      // remove the skip link. If the mobile hamburger is visible, remove the link.
      var $sn = $('#secondary-navigation', context).length;
      if (!$sn) {
        $('.su-skipnav--secondary', context).remove();
      }

      // Check for search box and move the second block to the mobile navigation.
      // Hide it and then only show for mobile sites.
      const $search = $('.su-masthead .su-site-search', context);
      if ($search.length) {
        const $clonedSearch = $search.clone();
        $clonedSearch.addClass('search-block-form');
        // Adjust the parent id attribute.
        $clonedSearch.attr('id', 'block-stanford-basic-search-mobile');
        // Adjust all the children id attributes and fix any labels.
        $clonedSearch.find('[id]').each((i, element) => {
          const idAttribute = $(element).attr('id');
          $clonedSearch.find(`[for="${idAttribute}"]`).attr('for', `${idAttribute}-mobile`);
          $(element).attr('id', `${idAttribute}-mobile`);
        });

        $clonedSearch.prependTo('.su-masthead .su-multi-menu > ul', context)
          .wrap('<li class="su-mobile-site-search"></li>');
      }

      // Add an outline class to the page-content region if local tasks are
      // available.
      var localTab = $('#block-stanford-basic-local-tasks', context);
      if (localTab.length) {
        $('.page-content', context).addClass('stanford-basic--outline');
      }

      var userLogin = $('.page-user-login', context);
      if (userLogin) {
        $('.su-back-to-site', context).removeClass('hidden');
      }

      backToTop();
      $(window).scroll(backToTop)

      $(once('back-to-top', '#back-to-top', context)).click((e) => {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $('#page-content').attr('tabIndex', '-1').focus();
      })

      /**
       * Hide show back to top links.
       */
      function backToTop() {
        if ($(window).scrollTop() >= ($(window).height() * 3)) {
          $('#back-to-top').fadeIn();
        } else {
          $('#back-to-top').fadeOut();
        }
      }

      /**
       * Open and close on the filter menu: News, People, Publications, Events
       */
      $('.topics__collapsable-menu', context).click(function () {
        $(this).toggleClass('show');
        if ($(this).siblings('.menu').css('display') !== 'none') {
          $(this).attr('aria-expanded', 'true');
        }
        else {
          $(this).attr('aria-expanded', 'false');
        }
      });

      $(once('faq-expand-all', '.ptype-stanford-faq', context)).each((index, faq) => {
        if ($('.accordion__title', faq).length < 2 || $('.ptype-stanford-faq', faq).length) {
          return;
        }

        const $button = $(
          '<button class="expand-collapse-button expand-all su-button--secondary">' +
          '<span class="expand-collapse">Expand</span> All' +
          '<span class="visually-hidden"> Items below.</span>' +
          '</button>',
        );

        $button.click(function () {
          $button.toggleClass('expand-all').toggleClass('collapse-all');
          const expanded = !$button.hasClass('expand-all');

          $('span', $button).text(expanded ? 'Collapse' : 'Expand');
          $(`.accordion__title[aria-expanded="${expanded ? 'false' : 'true'}"]`, faq).click();
        });

        const $headline = $('.su-faq-headline', faq);
        if ($headline.length) {
          $headline.append($('<div class="button-wrapper">').append($button));
        } else {
          $(faq).prepend($('<div class="button-wrapper clearfix">').append($button));
        }
      });

    })(jQuery, once);
  },

  // Detach Example.
  detach() {
    // console.log("Detached.");
  }
};
