/**
 * @file
 * Custom functionality for the Content - Image paragraph type.
 */
((Drupal, $) => {

  /**
   * Toogle the display of the details element.
   *
   * @param {boolean} checked
   *   Status of the checkbox.
   * @param {object} $details
   *   Instance of a jQuery object.
   */
  const toogleDetails = (checked, $details) => {
    if (checked) {
      $details.fadeIn();
    }
    else {
      $details.fadeOut();
    }
  };

  Drupal.behaviors.adminimalMedelaContentImage = {
    attach: (context, settings) => {
      const $fieldImageZoomable = $(".field--name-field-h-image-zoomable", context);

      $fieldImageZoomable.once().each((index, element) => {
        const $element = $(element);
        const $details = $element.siblings(".health-image__figure-settings");
        const $checkbox = $(element).find("input");

        // Display details if the checkbox status is checked.
        if ($checkbox.prop("checked")) {
          toogleDetails(true, $details);
        }

        // Toogle the display of the details element when status of checkbox 
        // changes.
        $checkbox.change((event) => {
          const checked = event.currentTarget.checked;
          toogleDetails(checked, $details);
        });
      });
    }
  };

})(Drupal, jQuery);
