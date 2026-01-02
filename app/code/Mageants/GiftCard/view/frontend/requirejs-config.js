/**
 * Mageants GiftCard Magento2 Extension                           
 */
var config = {
    map: {
        '*': {
            giftcertificate: 'Mageants_GiftCard/js/giftcertificate',
            loadTotals: 'Mageants_GiftCard/js/loadTotals',
            'Magento_Checkout/template/minicart/item/default.html': 'Mageants_GiftCard/template/minicart/item/default.html'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'Mageants_GiftCard/js/model/place-order-mixin': true
            }
        }
    }
}