(function( $ ) {
	'use strict';
	jQuery(document).ready(function($) {
	    const $categoryRadios = $('.digiwoo-selected-product-categories input[type="radio"]');
	    const $addonProducts = $('.digiwoo-add-on-products .addon-product');

	    $categoryRadios.on('change', function() {
	        const selectedCategory = $(this).val();
	        
	        $addonProducts.each(function() {
	            const hideRule = $(this).data('hide-rule');
	            if (hideRule === selectedCategory) {
	                $(this).hide();
	            } else {
	                $(this).show();
	            }
	        });
	    });
	});




})( jQuery );