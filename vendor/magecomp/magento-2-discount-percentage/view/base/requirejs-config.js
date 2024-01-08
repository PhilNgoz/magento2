var config = {
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Magecomp_Discountpercentage/js/model/skuswitch': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Magecomp_Discountpercentage/js/model/swatch-skuswitch': true
            }
        }
    }
};
