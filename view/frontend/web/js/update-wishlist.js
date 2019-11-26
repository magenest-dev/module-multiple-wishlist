/**
 * Created by chung on 4/6/17.
 */
require(
    [
        'jquery'
    ],
    function ($) {
        'use strict';
        console.log('update-wl');
        $("button#update-main").on('click', function (event) {
            event.preventDefault();
            var updateForm = $("form#update-main-form"),
                mainDes = $("textarea.main-description"),
                mainQty = $("input.main-qty");
            mainDes.appendTo(updateForm);
            mainQty.appendTo(updateForm);
            updateForm.css('display', 'none');
            updateForm.submit();
        });

        $("button#update-wishlist").on('click', function (event) {
            event.preventDefault();
            var updateForm = $("form#update-form"),
                descriptions = $("textarea.description-area"),
                qtyInputs = $("input.qty-input");
            descriptions.appendTo(updateForm);
            qtyInputs.appendTo(updateForm);
            updateForm.css('display', 'none');
            updateForm.submit();
        })
    }
);
