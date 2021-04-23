/**
 * @file
 * Custom functionality for publication rules.
 */
(function ($, Drupal, window) {

  'use strict';

  Drupal.behaviors.adminimalMedelaPublicationRules = {
    attach: function (context, settings) {

      // Check what content type are selected and decide if publication type
      // should be displayed.
      $(".listing-embedded-filters--content-types").each(function (i) {

        let container = $(this);

        var showPub = true;
        container.find("input:checkbox").each(function (i) {

          var checkbox = $(this);

          let label = checkbox.val();
          let status = checkbox.is(":checked");

          if (label !== "h_publication" && status && label !== "1" && label !== 1) {
            showPub = false;
          }

          if (label === "h_publication" && !status) {
            showPub = false;
          }
        });

        if (showPub) {
          container.siblings(".listing-embedded-filters--publication-type").show();
        }
        else {
          container.siblings(".listing-embedded-filters--publication-type").find("input:checkbox").each(function (i) {
            $(this).prop('checked', false);
          });
          container.siblings(".listing-embedded-filters--publication-type").hide();
        }

      });

      //On update of Content types, only show Publication type if only
      // Publication is selected.
      $(".listing-embedded-filters--content-types").find("input:checkbox").on('click', function (e) {

        var showPub = true;
        var checked = $(this);

        checked.closest(".listing-embedded-filters--content-types").find("input:checkbox").each(function (i) {

          let checkbox = $(this);

          let label = checkbox.val();
          let status = checkbox.is(":checked");

          if (label !== "h_publication" && status && label !== "1" && label !== 1) {
            showPub = false;
          }

          if (label === "h_publication" && !status) {
            showPub = false;
          }
        });

        if (showPub) {
          checked.closest(".listing-embedded-filters--content-types").siblings(".listing-embedded-filters--publication-type").show();
        }
        else {
          checked.closest(".listing-embedded-filters--content-types").siblings(".listing-embedded-filters--publication-type").find("input:checkbox").each(function (i) {
            $(this).prop('checked', false);
          });
          checked.closest(".listing-embedded-filters--content-types").siblings(".listing-embedded-filters--publication-type").hide();
        }
      });

    }
  };

})(jQuery, Drupal, window);
