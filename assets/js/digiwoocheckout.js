(function( $ ) {
	'use strict';
	jQuery(document).ready(function($) {
		const ajaxurl = digiwooScriptAjaxurl.ajax_url;
	    const $categoryRadios = $('.digiwoo-selected-product-categories input[name="product_category"]');
	    const $addonProducts = $('.digiwoo-add-on-products .addon-product');
	    const $productsContainer = $('.products-container'); // Assume you wrap your products list in a div with class 'products-container'

	    // Function to update the total price
	    const updateTotalPrice = () => {
	    	const $productRadios = $('input[type="radio"][name="default_product"]');
		    const $addonCheckboxes = $('input[type="checkbox"][name="addon_product"]');
		    const $totalPriceDiv = $('#total-price span');

	        let basePrice = parseFloat($productRadios.filter(':checked').data('price') || 0);
	        let totalPrecentage = 0;

	        $addonCheckboxes.filter(':checked').each(function() {
	            totalPrecentage += parseFloat($(this).data('percentage'));
	        });

	        const totalPrice = basePrice + (basePrice * totalPrecentage);
	        $totalPriceDiv.text(totalPrice.toFixed(2)); // Displaying price with two decimal points
	    };

	    $categoryRadios.on('change', function() {
	        const selectedCategory = $(this).val();
	        $('input[name="default_product"]').prop('checked', false);
	        $('input[name="addon_product"]').prop('checked', false);
	        
	        $addonProducts.each(function() {
	            const hideRule = $(this).data('hide-rule');
	            if (hideRule == selectedCategory) {  // Using double equals to allow type coercion
	                $(this).hide();
	            } else {
	                $(this).show();
	            }
	        });

	        $.ajax({
	            url: ajaxurl, // This variable is automatically defined by WordPress when you enqueue your script using wp_enqueue_script()
	            type: 'POST',
	            data: {
	                action: 'dgc_fetch_products_by_category',
	                category_id: selectedCategory,
	            },
	            success: function(response) {
	                if (response) {
	                    $productsContainer.html(response);
	                }
	            }
	        });
	    });    
	    

	    // Event delegation for dynamic elements
		$(document).on('change', 'input[name="default_product"]', updateTotalPrice);
		$(document).on('change', 'input[name="addon_product"]', updateTotalPrice);



	    
	});

})( jQuery );