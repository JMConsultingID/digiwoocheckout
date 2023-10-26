(function( $ ) {
	'use strict';
	document.addEventListener('DOMContentLoaded', function() {
	    const categoryRadios = document.querySelectorAll('.digiwoo-selected-product-categories input[type="radio"]');
	    const addonProducts = document.querySelectorAll('.digiwoo-add-on-products .addon-product');

	    categoryRadios.forEach(radio => {
	        radio.addEventListener('change', function() {
	            const selectedCategory = this.value;
	            
	            addonProducts.forEach(product => {
	                const hideRule = product.getAttribute('data-hide-rule');
	                if (hideRule === selectedCategory) {
	                    product.style.display = 'none';
	                } else {
	                    product.style.display = 'block';
	                }
	            });
	        });
	    });
	});




})( jQuery );