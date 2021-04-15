/**
 * @file
 * Custom functionality for publication rules.
 */
(function ($, Drupal, window) {

  'use strict';

  Drupal.behaviors.adminimalMedelaPublicationRules = {
    attach: function (context, settings) {

      $(".listing-embedded-filters--content-types").each(function (i) {

        var showPub = true;
        $(this).find("input:checkbox").each(function (i) {
          let label = $(this).val();
          let status = $(this).is(":checked");

          if (label !== "h_publication" && status && label !== "1" && label !== 1) {
            showPub = false;
          }

          if (label === "h_publication" && !status) {
            showPub = false;
          }
        });

        if (showPub) {
          $(this).siblings(".listing-embedded-filters--publication-type").show();
        } else {
          $(this).siblings(".listing-embedded-filters--publication-type").find("input:checkbox").each(function (i) {
            $(this).prop('checked', false);
          });
          $(this).siblings(".listing-embedded-filters--publication-type").hide();
        }

      });


      $(".listing-embedded-filters--content-types").find("input:checkbox").on('click', function (e) {

        var showPub = true;
        $(this).closest(".listing-embedded-filters--content-types").find("input:checkbox").each(function (i) {
          let label = $(this).val();
          let status = $(this).is(":checked");

          if (label !== "h_publication" && status && label !== "1" && label !== 1) {
            showPub = false;
          }

          if (label === "h_publication" && !status) {
            showPub = false;
          }
        });

        if (showPub) {
          $(this).closest(".listing-embedded-filters--content-types").siblings(".listing-embedded-filters--publication-type").show();
        } else {
          $(this).closest(".listing-embedded-filters--content-types").siblings(".listing-embedded-filters--publication-type").find("input:checkbox").each(function (i) {
            $(this).prop('checked', false);
          });
          $(this).closest(".listing-embedded-filters--content-types").siblings(".listing-embedded-filters--publication-type").hide();
        }
      });

    }
  };

})(jQuery, Drupal, window);
