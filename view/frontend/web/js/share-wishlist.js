require(
    [
        'jquery'
    ],
    function ($) {
        'use strict';

        $(".share-wishlist").on('click', function (event) {
            // console.log($(event.target).val());
            var wishlistId = $('#share-gmail').val();
            var url = $("input#base-url").val() + 'wishlist/index/share/?wishlist_id='+ wishlistId;
            document.location = url;
            console.log(url);
            });
        
        var myParam = location.search.split('wishlist_id=')[1];
        console.log(myParam);

        if ((typeof myParam !== "undefined") && myParam.indexOf('/') > 0) {
            myParam = myParam.substr(0,myParam.indexOf('/'));
        }
        $("#form-validate").append('<input type="hidden" name="wishlistId" value="'+ myParam +'"/>');
    }
);
