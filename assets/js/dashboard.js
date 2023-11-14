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

  // var $wrapper = $('.words-fields-list');
  // $wrapper.on('click', '.add-another-word', function(e) {
  //   e.preventDefault();
  //   // Get the data-prototype explained earlier
  //   var prototype = $wrapper.data('prototype');
  //   // get the new index
  //   var index = $wrapper.data('index');
  //   // Replace 'name' in the prototype's HTML to
  //   // instead be a number based on how many items we have
  //   var newForm = prototype.replace(/name/g, index);
  //   // increase the index with one for the next item
  //   $wrapper.data('index', index + 1);
  //   // Display the form in the page before the "new" link
  //   $(this).before(newForm);
  // });
});
