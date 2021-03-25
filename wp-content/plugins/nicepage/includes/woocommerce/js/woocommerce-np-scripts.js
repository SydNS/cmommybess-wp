jQuery(document).ready(function($) {
    jQuery(document).on('click', '.u-product .u-btn.u-product-control', function(e) {
        e.preventDefault();
        jQuery(this).parents('.u-product').find('.single_add_to_cart_button').click();
    });
    function changePrice() {
        if (jQuery('.woocommerce-variation-price').length) {
            var priceControl = jQuery('.u-product-price:visible');
            if (priceControl.length > 1) {
                priceControl.each(function(index) {
                    if (index === 0) { return; }
                    priceControl[index].remove();
                });
            }
            priceControl.find('.u-price').html(jQuery('.woocommerce-variation-price .price ins').not(':visible').html());
            priceControl.find('.u-old-price').html(jQuery('.woocommerce-variation-price .price del').not(':visible').html());
        }
    };
    jQuery(document).on('change', '.u-product-variant select', changePrice);
    function changeQuantity() {
        if (jQuery('.quantity').length) {
            jQuery('form .quantity input.qty').val(jQuery('.u-quantity-input .u-input').val());
        }
    };
    jQuery(document).on('change', '.u-quantity-input', changeQuantity);
});