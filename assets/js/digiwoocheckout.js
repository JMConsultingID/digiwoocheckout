(function( $ ) {
	'use strict';
	jQuery(document).ready(function($) {
		const ajaxurl = digiwooScriptAjaxurl.ajax_url;
	    const $categoryRadios = $('.digiwoo-selected-product-categories input[name="product_category"]');
	    const $addonProducts = $('.digiwoo-add-on-products .addon-product');
	    const $productsContainer = $('.products-container'); // Assume you wrap your products list in a div with class 'products-container'

	    $('input[name="default_product"]').prop('disabled', true);
	    $('input[name="addon_product[]"]').prop('disabled', true);
	    $('.btn').prop('disabled', true);

	    // Function to update the total price
	    function updateTotalPrice() {
	    	const $productRadios = $('input[type="radio"][name="default_product"]');
		    const $addonCheckboxes = $('input[type="checkbox"][name="addon_product[]"]');
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
	        $('input[name="addon_product[]"]').prop('checked', false);
	        $('input[name="addon_product[]"]').prop('disabled', true);
	        $('.products-container').addClass('loading');    
	        $('.digiwoo-add-on-products').addClass('loading');	
	        
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
	                    $('input[name="default_product"]').prop('disabled', true);
	                    updateTotalPrice();
	                }
	            },
	            complete:function(){
	            	$('.products-container').removeClass('loading'); 
                	$('.digiwoo-add-on-products').removeClass('loading');
                	$('input[name="default_product"]').prop('disabled', false);	                    	
                }
	        });
	    });    
	    

	    $(document).on('change', 'input[name="default_product"]', function() {
	    	$('input[name="addon_product[]"]').prop('checked', false);
	    	$('input[name="addon_product[]"]').prop('disabled', false);
	    	$('.btn').prop('disabled', false);
	    	updateTotalPrice();
	    });

	    $(document).on('change', 'input[name="addon_product[]"]', function() {
	    	updateTotalPrice();
	    });


	    $('#proceedWithPayment').click(function(e) {
	    	e.preventDefault();
	    	
	        let selectedProduct = $('input[name="default_product"]:checked').val();
	        
	        let addonProducts = [];
	        $('input[name="addon_product[]"]:checked').each(function() {
	            addonProducts.push($(this).val());
	        });

	        let totalPrice = $('#total-price span').text(); // Replace with your div's id or selector
	        
	        $.ajax({
	            url: ajaxurl,
	            type: 'POST',
	            data: {
	                action: 'create_order',
	                product_id: selectedProduct,
	                addon_products: addonProducts,
	                total_price: totalPrice
	            },
	            success: function(response) {
	                if (response.success) {
				        window.location.href = '/checkout/order-pay/' + response.order_id + '?pay_for_order=true';
				    }
	            }
	        });
	    });
	    
	});

})( jQuery );