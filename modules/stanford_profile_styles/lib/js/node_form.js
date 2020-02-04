/**
 * @file
 * Stops page from changing when user is posting.
 */

(function ($, Drupal) {
  'use strict';
  // Allow Save Submit button.
  var save = false;
  // Flag the form has been edited.
  var edit = false;

  Drupal.behaviors.StanfordProfileStylesNodeProtection = {
    attach: function (context, settings) {
      function addListeners() {
        // Add listeners for all types of data entry.
        $("input, button, :input", context).once('form-protection').on('blur change click dblclick keydown mousedown select submit', function (e) {
          edit = true;
          save = false;

          // Node save button.
          if ($(this).attr('value') == 'Save') {
            save = true;
          }
        });
      }

      addListeners();
      // Periodically add listeners to buttons to check for activity
      setTimeout(addListeners, 2000);

      // Handle backbutton, exit etc.
      window.onbeforeunload = function () {
        // Add CKEditor support.
        if (typeof (CKEDITOR) != 'undefined' && typeof (CKEDITOR.instances) != 'undefined') {
          for (var i in CKEDITOR.instances) {
            if (CKEDITOR.instances[i].checkDirty()) {
              edit = true;
              break;
            }
          }
        }

        if (edit && !save) {
          return (Drupal.t("You will lose all unsaved work."));
        }
      }
    }
  };
})(jQuery, Drupal);
