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

	    const $productRadios = $('input[type="radio"][name="default_product"]');
	    const $addonCheckboxes = $('input[type="checkbox"][name="addon_product"]');
	    const $totalPriceDiv = $('#total-price span');

	    // Function to update the total price
	    const updateTotalPrice = () => {
	        let basePrice = parseFloat($productRadios.filter(':checked').data('price') || 0);
	        let totalPrecentage = 0;

	        $addonCheckboxes.filter(':checked').each(function() {
	            totalPrecentage += parseFloat($(this).data('percentage'));
	        });

	        const totalPrice = basePrice + (basePrice * totalPrecentage);
	        $totalPriceDiv.text(totalPrice.toFixed(2)); // Displaying price with two decimal points
	    };

	    // Event listeners
	    $productRadios.on('change', updateTotalPrice);
	    $addonCheckboxes.on('change', updateTotalPrice);


	    
	});

})( jQuery );