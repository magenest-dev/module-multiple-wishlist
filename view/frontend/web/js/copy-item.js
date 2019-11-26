/**
 * Created by chung on 3/30/17.
 */
require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Customer/js/customer-data',
        'jquery/jquery.cookie'
    ],
    function ($, modal, customerdata) {
        'use strict';
        var modalOptions = {
            title: 'Wish List:',
            buttons: false,
        };
        var arayReturn = [];
        $("input[name=wishlist_radio]").on('change', function () {
            $("#emptyname").replaceWith(
                "<div style='color: red' id='emptyname' hidden='hidden'></div>");
        });
        $(".copy-item").on('click', function (event) {
            // console.log($(event.target).val());
            event.stopPropagation();
            event.preventDefault();
            var itemId = $(event.target).val();
            $("div#modal_content").modal(modalOptions).modal('openModal');
            $("form#wishlist-picker").css('display', '');
            $("input[name=wishlist_radio]").on('change', function () {
                var ifNew = ($("input[name=wishlist_radio]:checked", "#wishlist-picker").val() === 'new');
                $("input#new-wishlist-name").prop('disabled', !ifNew);
            });
            $("button#mwl-continue").unbind().on('click', {data: itemId}, function (event) {
                event.stopPropagation();
                event.preventDefault();
                var wishlist = $("input[name='wishlist_radio']:checked").val(),
                    newName = $("#new-wishlist-name").val();
                if ($('#wl-rd-new').is(':checked') && (newName.length === 0 || newName.startsWith(" "))) {
                    $("#emptyname").replaceWith(
                        "<div style='color: red' id='emptyname'>This is a required field.</div>");
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                } else {
                    var url = $("input#base-url").val() + 'multiplewishlist/item/copy/itemId/' + itemId + '/wishlist/' + wishlist + '/newName/' + newName;
                    window.location.href = url;
                    $("button#mwl-continue").attr("disabled", true);
                }
            });
        });
    }
);
