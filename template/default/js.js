$(document).ready(function () {


    $(".nav-stacked li a").on('click', function (event) {
        event.preventDefault();

        if ($(this).next('ul').hasClass('nav-stacked')) {

            if ($(this).next('ul').hasClass('menu-click'))
                $(this).next('ul').removeClass('menu-click').css('display', 'none');
            else
                $(this).next('ul').addClass('menu-click').css('display', 'block');

        } else
            window.location.href = $(this).attr('href');
    });



    $('.nav-stacked li a').each(function () {
        if ($(this).next('ul').hasClass('nav-stacked'))
            $(this).append('<span class="glyphicon glyphicon-menu-right pull-right small"></span>');
    });

    $(".search-mobil-activ").on('click', function () {
        $('.search-mobil-display').toggleClass('hide');
    });

    $(".search-mobil").on('input', function () {
        var words = $(this).val();
        if (words.length > 2) {
            $.ajax({
                type: "POST",
                url: "/search.php",
                dataType: 'json',
                data: {
                    words: words,
                },
                success: function (data)
                {

                    // Результат поиска
                    if (data != 'false') {

                        if (data != $(".search-mobil").attr('data-content')) {

                            var result = "";

                            $.each(data, function (index, element) {
                                result += '<li><a href="' + element.file + '">' + element.title + '<div>' + element.description + '</div></a></li>';
                            });



                            $(".search-mobil").attr('data-content', '<ul class="nav nav-pills nav-stacked">' + result + '</ul>');

                            $(".search-mobil").popover('show');
                        }
                    } else
                        $(".search-mobil").popover('hide');
                }
            });
        } else {
            $(".search-mobil").attr('data-content', '');
            $(".search-mobil").popover('hide');

        }

    });

    $(".search").on('input', function () {
        var words = $(this).val();
        if (words.length > 2) {
            $.ajax({
                type: "POST",
                url: "/search.php",
                dataType: 'json',
                data: {
                    words: words,
                },
                success: function (data)
                {

                    // Результат поиска
                    if (data) {

                        if (data != $(".search").attr('data-content')) {

                            var result = "";

                            $.each(data, function (index, element) {
                                result += '<li><a href="' + element.file + '">' + element.title + '<div>' + element.description + '</div></a></li>';
                            });



                            $(".search").attr('data-content', '<ul class="nav nav-pills nav-stacked">' + result + '</ul>');

                            $(".search").popover('show');
                        }
                    } else
                        $(".search").popover('hide');
                }
            });
        } else {
            $(".search").attr('data-content', '');
            $(".search").popover('hide');

        }

    });

    $(window).scroll(function () {
        var a = $(window).scrollTop() + 50;
        $('#menu').css('max-height', 700 + a);
    });

});
