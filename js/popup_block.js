(function ($, Drupal, drupalSettings) {
  if (drupalSettings['popup_block'] != undefined) {
    var popupImageUri = drupalSettings['popup_block']['popupBlockImage'];
    if (popupImageUri.length > 0) {
      $("#custom-block-popup").attr("src", popupImageUri);
    }
  }
})(jQuery, Drupal, drupalSettings);
