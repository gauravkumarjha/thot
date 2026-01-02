

define(

    [

        'uiComponent',

    ],

    function (Component, escaper) {

        "use strict";

        var quoteItemData = window.checkoutConfig.quoteItemData;

        return Component.extend({

            defaults: {

                template: 'Mageplaza_Simpleshipping/summary/item/details'

            },
       

            quoteItemData: quoteItemData,

            getValue: function (quoteItem) {

                return quoteItem.name;

            },

            getCustomValue: function (quoteItem) {

                var item = this.getItem(quoteItem.item_id);

                if (item.customshippingcharge) {
                    let customShippingCharge = item.customshippingcharge;
                 
                    if (typeof customShippingCharge === 'number' && !isNaN(customShippingCharge)) {

                        // Assuming you want to add the currency symbol (e.g., $) before the number
                        var formattedShippingCharge = '₹' + parseInt(customShippingCharge).toFixed(2);
                      

                    } else {
                        var formattedShippingCharge = customShippingCharge;
                    }
                    return formattedShippingCharge;

                } else {

                    return '';

                }
 
            },

            getItem: function (item_id) {

                var itemElement = null;

                _.each(this.quoteItemData, function (element, index) {

                    if (element.item_id == item_id) {

                        itemElement = element;

                    }

                });

                return itemElement;

            }

        });

    }

);