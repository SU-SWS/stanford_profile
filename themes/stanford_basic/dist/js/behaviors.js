/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 903:
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

/***/ 5741:
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
      // Validate there is a skip link anchor for the main content. If not,
      // default to #page-content.
      var $mc = $('#main-content', context).length;
      if (!$mc) {
        $('.su-skipnav--content', context).attr('href', '#page-content');
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
        if ($(this).siblings('.menu').css('display') != 'none') {
          $(this).attr('aria-expanded', 'true');
        } else {
          $(this).attr('aria-expanded', 'false');
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
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";

// EXTERNAL MODULE: ./src/js/theme/menu/StickyHeaderOnScroll.js
var StickyHeaderOnScroll = __webpack_require__(903);
;// CONCATENATED MODULE: ./src/js/theme/menu/index.js

;// CONCATENATED MODULE: ./src/js/theme/index.js
/**
 * Primary roll up file
 */

// The Local Task Menu

// EXTERNAL MODULE: ./src/js/stanford_basic.behavior.js
var stanford_basic_behavior = __webpack_require__(5741);
;// CONCATENATED MODULE: ./src/js/behaviors.js
// Theme code.


}();
/******/ })()
;