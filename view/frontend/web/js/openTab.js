require(
    [
        'jquery',
        'jquery/jquery.cookie'
    ],
    function ($) {
        'use strict';
        $(document).ready(function () {
            var id = $('.wishlistIdcookie').val();
            if (id) {
                var idTabWishList = '#wl' + id;
                $('button').each(function () {
                    if ($(this).attr('class') === 'tablinks active') {
                        $(this).toggleClass('active');
                    }
                });
                $(idTabWishList).click(function () {
                    $(idTabWishList).removeClass();
                    $(this).addClass('tablinks active');
                });
                $(idTabWishList).click();
            }
        });
    });