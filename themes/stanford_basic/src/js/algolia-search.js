(function ($, settings) {
  'use strict';
  Drupal.behaviors.algoliaSearch = {
    attach: function () {
      const searchClient = algoliasearch(drupalSettings.stanfordAlgolia.appId, drupalSettings.stanfordAlgolia.searchKey);

      const search = instantsearch({
        indexName: drupalSettings.stanfordAlgolia.index,
        searchClient,
      });

      search.addWidgets([
        instantsearch.widgets.searchBox({
          container: '#algolia-search',
        }),

        instantsearch.widgets.hits({
          container: '#algolia-results',
          templates: {
            item(hit, { html, components }) {
              return html`
                <h2><a href="${hit.url}">${components.Highlight({ hit, attribute: 'title' })}</a></h2>
                <p>${components.Snippet({ hit, attribute: 'rendered' })}</p>
              `;
            },
          },
        }),
      ]);

      search.start();
    },
  };

})(jQuery, drupalSettings);
