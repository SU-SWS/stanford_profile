/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 8035:
/***/ (function() {

var header = document.getElementById('block-stanford-basic-local-tasks');
var sticky = 0;
if (header) {
  sticky = header.getBoundingClientRect().top;
  window.onscroll = function () {
    stickyHeaderOnScroll();
  };
}

/**
 * Stick the local block tasks to the top of the window.
 */
function stickyHeaderOnScroll() {
  var toolbarHeight = 0;
  var toolbarOpen = document.body.classList.contains('toolbar-tray-open');
  if (toolbarOpen === true) {
    toolbarHeight = 79;
  } else {
    toolbarHeight = 39;
  }
  if (window.pageYOffset >= sticky - toolbarHeight) {
    header.classList.add('sticky');
    header.style.marginTop = toolbarHeight + 'px';
  } else {
    header.classList.remove('sticky');
    header.style.marginTop = '0px';
  }
}

/***/ }),

/***/ 5644:
/***/ (function() {

/**
 * Behavior Example that works with Webpack.
 *
 * @see: https://www.npmjs.com/package/drupal-behaviors-loader
 *
 * Webpack wraps everything in enclosures and hides the global variables from
 * scripts so special handling is needed.
 */

window.Drupal.behaviors.stanford_basic = {
  // Attach Drupal Behavior.
  attach: function attach(context, settings) {
    (function ($, once) {
      // If some embed code contains a caption, make sure the figure respects
      // the iframe width of 100%.
      $('figure', context).each(function () {
        var $iframeWithin = $('iframe', this);
        var iframeWidth = $iframeWithin.attr('width');
        if ($iframeWithin.length && (!iframeWidth || iframeWidth === '100%')) {
          $(this).css('width', '100%');
        }
      });

      // Validate there is a skip link anchor for the main content. If not,
      // default to #page-content.
      var $title = $('h1', context);
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
      var $search = $('.su-masthead .su-site-search', context);
      if ($search.length) {
        var $clonedSearch = $search.clone();
        $clonedSearch.addClass('search-block-form');
        // Adjust the parent id attribute.
        $clonedSearch.attr('id', 'block-stanford-basic-search-mobile');
        // Adjust all the children id attributes and fix any labels.
        $clonedSearch.find('[id]').each(function (i, element) {
          var idAttribute = $(element).attr('id');
          $clonedSearch.find("[for=\"".concat(idAttribute, "\"]")).attr('for', "".concat(idAttribute, "-mobile"));
          $(element).attr('id', "".concat(idAttribute, "-mobile"));
        });
        $clonedSearch.prependTo('.su-masthead .su-multi-menu > ul', context).wrap('<li class="su-mobile-site-search"></li>');
      }

      // Check for empty navigation and add space.
      var $MenRegion = $('.region-menu', context);
      if ($MenRegion.children().length === 0) {
        $MenRegion.addClass("empty-menu");
      }

      // Move the utility button to the brand bar for mobile users
      var $utiltyBtn = $('.su-site-header-button', context);
      if ($utiltyBtn.length) {
        var $clonedutiltyBtn = $utiltyBtn.clone();
        $clonedutiltyBtn.appendTo('.su-brand-bar__container', context).wrap('<div class="su-mobile-utility-button"></div>');
      }

      // Move the Utiltiy links to the mobile menu. Decoupled menu addressed in the decoupled files.
      var $utility = $('.su-site-header-links', context);
      if ($utility.length) {
        var $clonedUtility = $utility.clone();
        $clonedUtility.addClass('utility-navigation-mobile');
        // for the Drupal menu
        $clonedUtility.insertBefore('.su-masthead .su-multi-menu > ul li:eq(1)', context).wrap('<li class="su-mobile-site-utility"></li>');

        // Change the utilty links to a list.
        var list = $("<ul class='stanford-basic-site-settings su-site-header-links utility-navigation-mobile'></ul>");
        $(".su-mobile-site-utility .su-site-header-links div").each(function () {
          list.append("<li>" + $(this).html() + "</li>");
        });
        $(".su-mobile-site-utility .su-site-header-links").first().replaceWith(list);
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
      $(window).scroll(backToTop);
      $(once('back-to-top', '#back-to-top', context)).click(function (e) {
        e.preventDefault();
        $("html, body").animate({
          scrollTop: 0
        }, "slow");
        $('#page-content').attr('tabIndex', '-1').focus();
      });

      /**
       * Hide show back to top links.
       */
      function backToTop() {
        if ($(window).scrollTop() >= $(window).height() * 3) {
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
        } else {
          $(this).attr('aria-expanded', 'false');
        }
      });
      $(once('faq-expand-all', '.ptype-stanford-faq', context)).each(function (index, faq) {
        if ($('.accordion__title', faq).length < 2 || $('.ptype-stanford-faq', faq).length) {
          return;
        }
        var $button = $('<button class="expand-collapse-button expand-all su-button--secondary">' + '<span class="expand-collapse">Expand</span> All' + '<span class="visually-hidden"> Items below.</span>' + '</button>');
        $button.click(function () {
          $button.toggleClass('expand-all').toggleClass('collapse-all');
          var expanded = !$button.hasClass('expand-all');
          $('span', $button).text(expanded ? 'Collapse' : 'Expand');
          $(".accordion__title[aria-expanded=\"".concat(expanded ? 'false' : 'true', "\"]"), faq).click();
        });
        var $headline = $('.su-faq-headline', faq);
        if ($headline.length) {
          $headline.append($('<div class="button-wrapper">').append($button));
        } else {
          $(faq).prepend($('<div class="button-wrapper clearfix">').append($button));
        }
      });
    })(jQuery, once);
  },
  // Detach Example.
  detach: function detach() {
    // console.log("Detached.");
  }
};

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
!function() {
"use strict";

// EXTERNAL MODULE: ./src/js/theme/menu/StickyHeaderOnScroll.js
var StickyHeaderOnScroll = __webpack_require__(8035);
;// ./src/js/theme/menu/index.js

;// ./src/js/theme/index.js
/**
 * Primary roll up file
 */

// The Local Task Menu

// EXTERNAL MODULE: ./src/js/stanford_basic.behavior.js
var stanford_basic_behavior = __webpack_require__(5644);
;// ./src/js/behaviors.js
// Theme code.


}();
/******/ })()
;