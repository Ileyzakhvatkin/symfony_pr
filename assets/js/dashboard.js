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
      let filesNames = `Выбрано файлов - ${files.length} (не более 5)`;
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
        // console.log(data);
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

  // Тестируем API
  const data = {
      'theme': 'FOOD',
      'keyword': {
        '0': 'Основа',
        '1': 'Основе',
        '2': 'Основе',
        '3': 'Основе',
        '4': 'Основой',
        '5': 'Основе',
        '6': 'Основы'
      },
      'title': 'Тестовая статья',
      'size': 2000,
      'words': [
          {
            'word': 'Инсталляция',
            'count': 2
          },
          {
            'word': 'Изоляция',
            'count': 4
          },
      ],
    'images': [
      'http://zis-symfony.tw1.ru/demo/img-1.jpg',
      'http://zis-symfony.tw1.ru/demo/img-2.jpg',
      'http://zis-symfony.tw1.ru/demo/img-3.jpg',
    ]
  };

  $('[data-id=apiBtn]').on('click', (e) => {
    e.preventDefault();
    console.log('Click API');
    $.ajax({
        url: '/api/article_create',
        method: 'POST',
        data: JSON.stringify(data),
        dataType: 'json',
    }).then((resp) => {
        console.log(resp);
    });
  })

});
