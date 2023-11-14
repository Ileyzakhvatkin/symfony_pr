import $ from 'jquery';

$(function() {
  "use strict";
  
  $("#menu-toggle").on('click', function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
  });

  // console.log($('.custom-file'));
  $('.custom-file').each(function () {
    const $container = $(this);

    $container.on('change', '.custom-file-input', function (event) {
      $container.find('.custom-file-label').html(event.currentTarget.files[0].name);
    });
  });

});
