require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'domReady!'
    ],
    function ($,modal) {
     $("#add-wishlist").on('click', function () {
            var check = true;
            var val = $('#new-name').val();
            var length = val.length;
            var countPresent = parseInt($('#countWishList').val());//1 thay cho main
            var countLimit = parseInt($('#numberWishList').val());
           if(length > 20)
           {
               $("#new-name-error").attr("hidden",true);
               $("#error-view").replaceWith(
                   "<div style='color: red'>The value exceeds maximum length of 20 characters</div>");
               check = false;
           }
         if((length === 0 || val.startsWith(" ")) && countPresent < countLimit)
         {
             $("#error-view").replaceWith(
                 "<div style='color: red'>This is a required field.</div>");
             check = false;
         }
         if(countPresent >= countLimit){
             $("#error-modal").replaceWith(
                 "<div hidden='hidden' id='error-modal'>The number of wishlists exceed the limitation("+countLimit+")</div>");
             var options = {
                 type: 'popup',
                 innerScroll: true,
                 buttons: [{
                     text: $.mage.__('Continue'),
                     class: '',
                     click: function () {
                         this.closeModal();
                     }
                 }]
             };
             var popup = modal(options, $('#error-modal'));
             $('#error-modal').modal('openModal');
             check = false;
         }
         if(check === true)
         {
             $( "#add-new" ).submit();
             $("#add-wishlist").attr("disabled", true);
         }else {
             return false;
         }
        });
    }
);



