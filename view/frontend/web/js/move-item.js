/**
 * Created by chung on 3/11/17.
 */
require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'jquery/jquery.cookie'
    ],
    function ($, modal) {
        'use strict';
        var Options = {
            title: 'Wish List:',
            buttons: false
        };
        $("input[name=wishlist_select]").on('change', function () {
            $("#emptyName").replaceWith(
                "<div style='color: red' id='emptyName' hidden='hidden'></div>");
        });
        var arrayReturn = [];
        $(".move-item").on('click', function (event) {
            // console.log($(event.target).val());
            event.stopPropagation();
            event.preventDefault();
            var itemId = $(event.target).val();
            $("div#modal_move_wishlist").modal(Options).modal('openModal');
            $("form#wishlist-picker-move").css('display','');
            $("input[name=wishlist_select]").on('change', function() {
                var ifNew = ($("input[name=wishlist_select]:checked", "#wishlist-picker-move").val() === 'new');
                $("input#new_wishlist").prop('disabled', !ifNew);
            });
            $("button#continue").unbind().on('click', {data: itemId}, function (event) {
                event.stopPropagation();
                event.preventDefault();
                var url = $("input#base-url").val() + 'multiplewishlist/item/move',
                    wishlist = $("input[name='wishlist_select']:checked").val(),
                    newWishList = $("#new_wishlist").val();

                if(($('#wl-rd-movenew').is(':checked') && (newWishList.length === 0 || newWishList.startsWith(" "))))
                {
                    $("#emptyName").replaceWith(
                        "<div style='color: red' id='emptyName'>This is a required field.</div>");
                    event.stopPropagation();
                    event.preventDefault();
                    return false;
                }else{
                    var url = $("input#base-url").val() + 'multiplewishlist/item/move/itemId/' + itemId + '/wishlist/' + wishlist + '/newName/' + newWishList;
                    window.location.href = url;
                    $("button#continue").attr("disabled", true);
                }
            });
        });
    }
);
