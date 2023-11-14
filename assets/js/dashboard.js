import $ from 'jquery';

$(function() {
  "use strict";
  
  $("#menu-toggle").on('click', function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
  });

  // Подставляем название в input file
  $('.custom-file').each(function () {
    const $container = $(this);

    $container.on('change', '.custom-file-input', (event) => {
      let files = event.currentTarget.files;
      // console.log(files)
      let filesNames = `Выбрано файлов - ${files.length}`;
      if(files.length > 5) {
        filesNames = `Выбрано файлов - ${files.length}. Будет загружено 5`
      }
      $container.find('.custom-file-label').html(filesNames);
    });
  });

  // Удаляем картинки
  document.querySelectorAll('[data-id=imageBlock]').forEach((el) => {
    const imgDeleteBtn = el.querySelector('[data-id=imageDelete]');
    imgDeleteBtn.addEventListener('click', (e) => {
      e.preventDefault();

      $.ajax({
        url: imgDeleteBtn.getAttribute('href'),
        method: 'POST',
      }).then((data) => {
        const { image } = data;
        if ( image == 'deleted' ) el.remove();
      });
    });
  });

  // Обновляем Token
  $('[data-id=tokenBtn]').on('click', (e) => {
    e.preventDefault();
    $.ajax({
      url: '/dashboard-token-update/',
      method: 'POST',
    }).then((data) => {
      const { status, token } = data;
      if ( status == 'updated' ) {
        $('[data-id=tokenValue]').text(token);
      }
    });

  })
});
