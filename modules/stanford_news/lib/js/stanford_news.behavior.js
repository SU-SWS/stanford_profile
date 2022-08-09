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
    (function ($) {


      $('.news-social-media', context).prepend('<div class="widget-wrapper-print"><a href="" class="share-print su-news-header__social-print"><i class="fas fa-print" aria-hidden="true"></i><span>' + Drupal.t('Print Article') + '</span></a></div>');
      $('.news-social-media', context).prepend('<div class="widget-wrapper-forward"><a href="" class="share-forward su-news-header__social-forward"><i class="fas fa-envelope" aria-hidden="true"></i><span>' + Drupal.t('Forward Email') + '</span></a></div>');
      $('.news-social-media', context).prepend('<div class="widget-wrapper-linkedin"><a href="" class="share-linkedin su-news-header__social-linkedin"><i aria-hidden="true"></i><span>' + Drupal.t('Stanford LinkedIn') + '</span></a></div>');
      $('.news-social-media', context).prepend('<div class="widget-wrapper-twitter"><a href="" class="share-twitter su-news-header__social-twitter"><i aria-hidden="true"></i><span>' + Drupal.t('Stanford Twitter') + '</span></a></div>');
      $('.news-social-media', context).prepend('<div class="widget-wrapper-fb"><a href="" class="share-fb su-news-header__social-facebook"><i aria-hidden="true"></i><span>' + Drupal.t('Stanford Facebook') + '</span></a></div>');

      // Get the current URL.
      var pathname = window.location;

      // Data.
      var shareTitle = $('div[property="dc:title"] h1', context).text();
      var shareSubtitle = $('.share-sub', context).text();

      // URL's
      var twurl = 'https://twitter.com/intent/tweet?url=' + encodeURI(pathname) + '&text=' + shareTitle + ' ' + shareSubtitle;
      var fburl = 'http://www.facebook.com/sharer.php?u=' + pathname + '&display=popup';
      var liurl = 'https://www.linkedin.com/shareArticle?mini=true&url=' + pathname + '&title=' + shareTitle + '&summary=' + shareSubtitle;

      // Going native rather than using forward module.
      var forurl = "mailto:?subject=" + document.title + "&body=" + encodeURI(document.location);

      // Going native rather than using print_pdf module.
      var prurl = 'window.print();return false;';

      // Add the URL's to anchors.
      $('.share-fb', context).attr({
        href: fburl
      });

      $('.share-twitter', context).attr({
        href: twurl
      });

      $('.share-linkedin', context).attr({
        href: liurl
      });

      $('.share-forward', context).attr({
        href: forurl
      });

      $('.share-print', context).attr({
        onclick: prurl
      });

    })(jQuery);
  }

};
