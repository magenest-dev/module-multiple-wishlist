/*jshint browser:true jquery:true*/
define([
    "jquery",
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data',
    "jquery/ui"
], function ($, modal, customerData) {
    "use strict";
    var isAjaxRunning = false;
    var modalOptions = {
        title: 'Add to Wishlist',
        buttons: false
    };
    var continueButton = $("button#mwl-continue");
    var countWishList = parseInt($('input#countWishList').val());
    var limitWishList = parseInt($('input#limitWishList').val());

    $("input[name=wishlist_radio]").on('change', function () {
        $("#error").replaceWith(
            "<div style='color: red' id='error' hidden='hidden'></div>");
    });

    function successAjax(result) {
        if (result.success) {
            if ($.isNumeric(result.id)) {
                var newRadio = $("input#wl-rd-new");
                $('<input>').attr({
                    name: 'wishlist_radio',
                    type: 'radio',
                    value: result.id,
                    id: 'wl-rd-' + result.id
                }).insertBefore(newRadio);

                $('<label>').attr({
                    for: 'wl-rd-' + result.id
                }).html(' ' + result.name).insertBefore(newRadio);

                $('<br>').insertBefore(newRadio);

            }
            // $('[data-placeholder="messages"]').append($('<div>').attr({
            //     class: 'message-success message success',
            //     'data-ui-id' : "message-success"
            // }).html('You added the product to wishlist'));
        } else {
            // $('[data-placeholder="messages"]').append($('<div>').attr({
            //     class: 'message-error message error',
            //     'data-ui-id' : "message-error"
            // }).html('Error'));
        }
    }

    function compeleteAjax() {
        location.reload(true);
        continueButton.removeProp('disabled');
        $("input#new-wishlist-name").val('');
        $("div#modal_content").modal(modalOptions).modal('closeModal');
        isAjaxRunning = false;
        var sections = ['cart', 'wishlist'];
        customerData.invalidate(sections);
        customerData.reload(sections, true);
        $("#error").replaceWith(
            "<div style='color: red' id='error' hidden='hidden'></div>");
        if(countWishList>=limitWishList){
            $("#wl-rd-new").hide();
            $("#new-wishlist-name").hide();
            $("#labelRadio").hide();
            $("#limit").prop('hidden',false);
            document.getElementById("wl-rd-main").checked = true;
        }
    }

    function continueFunction(event, params) {
        continueButton.prop('disabled', true);
        event.stopPropagation();
        event.preventDefault();

        var data = JSON.stringify(event.data.dataPost);
        var dataPost = JSON.parse(data);
        var productId = dataPost.data.product,
            url = $("input#base-url").val() + 'multiplewishlist/index/add',
            wishlist = $("input[name='wishlist_radio']:checked").val(),
            newName = $("#new-wishlist-name").val(),
            uenc = dataPost.data.uenc;

        if (!isAjaxRunning) {
            isAjaxRunning = true;
            $.ajax({
                url: url,
                async: true,
                method: "POST",
                dataType: "json",
                showLoader: true,
                cache:false,
                data: {
                    ajax: 'this is ajax',
                    data: params.data,
                    productId: productId,
                    wishlist: wishlist,
                    newName: newName,
                    uenc: uenc
                },
                success: function (result) {
                    successAjax(result);

                },
                complete: function () {
                    compeleteAjax();
                }
            });
        }
    }

    function customerLoginCheck(event) {
        var url = $("input#base-url").val() + 'multiplewishlist/customer/check';
        $.ajax({
            url: url,
            async: false,
            method: "GET",
            dataType: "json",
            success: function (result) {
                if (result === 'false') {
                    window.location.href = $("input#base-url").val() + 'customer/account/login/';
                    event.stopImmediatePropagation();
                }
            }
        });
        // var customer = customerData.get('customer');
        //
        // var customerRealValue = customer();
        // if (customerRealValue.firstname == undefined) {
        //     window.location.href = $("input#base-url").val() + 'customer/account/login/';
        //     return;
        // }
    }

    $("input[name=wishlist_radio]").on('change', function () {
        var ifNew = ($("input[name=wishlist_radio]:checked", "#wishlist-picker").val() === 'new');
        $("input#new-wishlist-name").prop('disabled', !ifNew);
    });

    $('[data-action="add-to-wishlist"]').on('click', function (event) {
        event.stopPropagation();
        event.preventDefault();

        customerLoginCheck();

        var params = $(event.currentTarget).data('post');
        $("div#modal_content").modal(modalOptions).modal('openModal');
        $("form#wishlist-picker").css('display', '');
        $("button.mwl-continue").on('click', {dataPost: params}, function (event) {
            if($('#wl-rd-new').is(':checked') && ($('input#new-wishlist-name').val().length === 0 || $('input#new-wishlist-name').startsWith === " ")) {
                $("#error").replaceWith(
                    "<div style='color: red' id='error'>This is a required field.</div>");
                return false;
            }else {
                continueFunction(event, params);
                $(this).off(event);
            }
        });
    });

    $.widget('mage.addToWishlist', {
        options: {
            bundleInfo: 'div.control [name^=bundle_option]',
            configurableInfo: '.super-attribute-select',
            groupedInfo: '#super-product-table input',
            downloadableInfo: '#downloadable-links-list input',
            customOptionsInfo: '.product-custom-option',
            qtyInfo: '#qty',
            multiple_wishlist: ''
        },
        _create: function () {
            this._bind();
        },
        _bind: function () {
            var self = this;
            var a = $('[data-action="add-to-wishlist"]');
            this.options.multiple_wishlist = a.attr('data-post');

            a.removeAttr('href');
            var options = this.options,
                dataUpdateFunc = '_updateWishlistData',
                changeCustomOption = 'change ' + options.customOptionsInfo,
                changeQty = 'change ' + options.qtyInfo,
                events = {};

            if ('productType' in options) {
                if (typeof options.productType === 'string') {
                    options.productType = [options.productType];
                }
            } else {
                options.productType = [];
            }

            events[changeCustomOption] = dataUpdateFunc;
            events[changeQty] = dataUpdateFunc;

            for (var key in options.productType) {
                if (options.productType.hasOwnProperty(key) && options.productType[key] + 'Info' in options) {
                    events['change ' + options[options.productType[key] + 'Info']] = dataUpdateFunc;
                }
            }
            this._on(events);
            this.bindFormSubmit();
        },
        _updateWishlistData: function (event) {
            var dataToAdd = {},
                isFileUploaded = false;
            if (event.handleObj.selector == this.options.qtyInfo) {
                this._updateAddToWishlistButton({});
                event.stopPropagation();
                return;
            }
            var self = this;
            $(event.handleObj.selector).each(function (index, element) {
                if ($(element).is('input[type=text]')
                    || $(element).is('input[type=email]')
                    || $(element).is('input[type=number]')
                    || $(element).is('input[type=hidden]')
                    || $(element).is('input[type=checkbox]:checked')
                    || $(element).is('input[type=radio]:checked')
                    || $(element).is('textarea')
                    || $('#' + element.id + ' option:selected').length
                ) {
                    dataToAdd = $.extend({}, dataToAdd, self._getElementData(element));
                    return;
                }
                if ($(element).is('input[type=file]') && $(element).val()) {
                    isFileUploaded = true;
                }
            });
            if (isFileUploaded) {
                this.bindFormSubmit();
            }
            this._updateAddToWishlistButton(dataToAdd);
            event.stopPropagation();
        },
        _updateAddToWishlistButton: function (dataToAdd) {
            var self = this;
            $('[data-action="add-to-wishlist"]').each(function (index, element) {
                var params = $(element).data('post');
                if (!params)
                    params = {'data': {}};

                if (!$.isEmptyObject(dataToAdd)) {
                    self._removeExcessiveData(params, dataToAdd);
                }

                params.data = $.extend({}, params.data, dataToAdd, {'qty': $(self.options.qtyInfo).val()});
                $(element).data('post', params);
            });
        },
        _arrayDiffByKeys: function (array1, array2) {
            var result = {};
            $.each(array1, function (key, value) {
                if (key.indexOf('option') === -1) {
                    return;
                }
                if (!array2[key])
                    result[key] = value;
            });
            return result;
        },
        _getElementData: function (element) {
            element = $(element);
            var data = {},
                elementName = element.data('selector') ? element.data('selector') : element.attr('name'),
                elementValue = element.val();
            if (element.is('select[multiple]') && elementValue !== null) {
                if (elementName.substr(elementName.length - 2) == '[]') {
                    elementName = elementName.substring(0, elementName.length - 2);
                }
                $.each(elementValue, function (key, option) {
                    data[elementName + '[' + option + ']'] = option;
                });
            } else {
                if (elementValue) {
                    if (elementName.substr(elementName.length - 2) == '[]') {
                        elementName = elementName.substring(0, elementName.length - 2);
                        if (elementValue) {
                            data[elementName + '[' + elementValue + ']'] = elementValue;
                        }
                    } else {
                        data[elementName] = elementValue;
                    }
                }
            }
            return data;
        },
        _removeExcessiveData: function (params, dataToAdd) {
            var dataToRemove = this._arrayDiffByKeys(params.data, dataToAdd);
            $.each(dataToRemove, function (key, value) {
                delete params.data[key];
            });
        },
        bindFormSubmit: function () {
            var self = this;
            $('[data-action="add-to-wishlist"]').on('click', function (event) {
                event.stopPropagation();
                event.preventDefault();

                customerLoginCheck();

                var element = $('input[type=file]' + self.options.customOptionsInfo),
                    params = $(event.currentTarget).data('post'),
                    form = $(element).closest('form'),
                    action = params.action;

                $("div#modal_content").modal(modalOptions).modal('openModal');
                $("form#wishlist-picker").css('display', '');
                $("button#mwl-continue").on('click', {dataPost: params}, function (event) {
                    if($('#wl-rd-new').is(':checked') && ($('input#new-wishlist-name').val().length === 0 || $('input#new-wishlist-name').startsWith === " ")) {
                        return false;
                    }else {
                        if($('#wl-rd-new').is(':checked')){countWishList = countWishList+1;}
                        continueFunction(event, params);
                        $( this ).off( event );
                    }
                });
            });
        }
    });
    return $.mage.addToWishlist;
});
