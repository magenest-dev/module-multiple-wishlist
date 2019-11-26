require(
    [
        'jquery',
        'Magento_Customer/js/customer-data',
        'Magento_Ui/js/model/messageList'
    ],
    function ($,customerData,messageList) {
        'use strict';
        $(".share-facebook").on('click', function (event) {
            // console.log($(event.target).val());
            var wishlistId = $(event.target).val();
            var url = $("input#base-url").val() + 'multiplewishlist/index/sharing?wishlist_id='+ wishlistId;
            document.location = url;
            console.log(url);

            var myParam = location.search.split('wishlist_id=')[1];
            console.log(myParam);

            if ((typeof myParam !== "undefined") && myParam.indexOf('/') > 0) {
                myParam = myParam.substr(0,myParam.indexOf('/'));
            }
            $("#form-validate").append('<input type="hidden" name="wishlistId" value="'+ myParam +'"/>');
        });

        $(".addToCart").submit(function(event){
            event.preventDefault(); //prevent default action
            var post_url = $(this).attr("action"); //get form action url
            var request_method = $(this).attr("method"); //get form GET/POST method
            var form_data = $(this).serialize(); //Encode form elements for submission

            $.ajax({
                url : post_url,
                type: request_method,
                data : form_data,
                showLoader: true,
            }).done(function(response){
                var url = $("input#base-url").val() + 'multiplewishlist/index/';
                var configUrl = response.backUrl ;
                if(configUrl !== url){
                    window.location.href = response.backUrl;
                }else{
                    var sections = ['cart'];
                    customerData.invalidate(sections);
                    customerData.reload(sections, true);
                    customerData.set('messages', {
                        messages: [{
                            type: 'success',
                            text: 'Added to cart.'
                        }]
                    });
                }
            });
        });

        $(".tocart-form").submit(function(event){
            event.preventDefault(); //prevent default action
            var post_url = $(this).attr("action"); //get form action url
            var request_method = $(this).attr("method"); //get form GET/POST method
            var form_data = $(this).serialize(); //Encode form elements for submission

            $.ajax({
                url : post_url,
                type: request_method,
                data : form_data,
                showLoader: true,
            }).done(function(response){
                var url = $("input#base-url").val() + 'wishlist/';
                var configUrl = response.backUrl ;
                if(configUrl !== url){
                    window.location.href = response.backUrl;
                }else{
                    var sections = ['cart'];
                    customerData.invalidate(sections);
                    customerData.reload(sections, true);
                    customerData.set('messages', {
                        messages: [{
                            type: 'success',
                            text: 'Added to cart.'
                        }]
                    });
                }
            });
        });
        $(".addAllToCart").submit(function(event){
            event.preventDefault(); //prevent default action
            var post_url = $(this).attr("action"); //get form action url
            var request_method = $(this).attr("method"); //get form GET/POST method
            var form_data = $(this).serialize(); //Encode form elements for submission

            $.ajax({
                url : post_url,
                type: request_method,
                data : form_data,
                showLoader: true,
            }).done(function(response){
                var sections = ['cart'];
                customerData.invalidate(sections);
                customerData.reload(sections, true);
                customerData.set('messages', {
                    messages: [{
                        type: 'success',
                        text: 'Added to cart.'
                    }]
                });
            });
        });
        $(".all-to-cart-main").submit(function(event){
            event.preventDefault(); //prevent default action
            var post_url = $(this).attr("action"); //get form action url
            var request_method = $(this).attr("method"); //get form GET/POST method
            var form_data = $(this).serialize(); //Encode form elements for submission

            $.ajax({
                url : post_url,
                type: request_method,
                data : form_data,
                showLoader:true,
            }).done(function(response){
                var sections = ['cart'];
                customerData.invalidate(sections);
                customerData.reload(sections, true);
                customerData.set('messages', {
                    messages: [{
                        type: 'success',
                        text: 'Added to cart.'
                    }]
                });
            });
        });

        $('#delete').click(function () {
            if (!confirm("Do you want to delete")){
                return false;
            }
        });
    }
);
