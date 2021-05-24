/**
 * @file
 * Custom functionality for publication rules.
 */
(function ($, Drupal, window) {

  'use strict';

  Drupal.behaviors.adminimalMedelaPublicationDescription = {
    attach: function (context, settings) {

      //Only show description field if a file is uploaded.
      $(".page-node-type-h-publication .vertical-tabs__menu-item").on("click", function () {
        var filattach = false
        $(".page-node-type-h-publication .paragraph-type--para-h-resource-part .item-container").each(function (index) {
          filattach = true;
        });

        if (filattach) {
          $(".page-node-type-h-publication .field--name-field-h-description").show();
        } else {
          $(".page-node-type-h-publication .field--name-field-h-description").hide();
        }

      });

    }
  };

})(jQuery, Drupal, window);
