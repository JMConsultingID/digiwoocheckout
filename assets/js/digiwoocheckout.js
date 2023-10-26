(function( $ ) {
	'use strict';
	jQuery(document).ready(function($) {
	    const $categoryRadios = $('.digiwoo-selected-product-categories input[name="product_category"]');
	    const $addonProducts = $('.digiwoo-add-on-products .addon-product');

	    $categoryRadios.on('change', function() {
	        const selectedCategory = $(this).val();
	        
	        $addonProducts.each(function() {
	            const hideRule = $(this).data('hide-rule');
	            if (hideRule == selectedCategory) {  // Using double equals to allow type coercion
	                $(this).hide();
	            } else {
	                $(this).show();
	            }
	        });
	    });
	});





})( jQuery );