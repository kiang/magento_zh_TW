// Font Replacement
Cufon.replace('.category-title h1,.footer h4, .product-view .product-shop .product-name h1,.page-title h1, .page-title h2,.wired-home .subscribe strong', {
	hover: true
});

jQuery(document).ready(function() {
	// Featured Products
    jQuery('#featured').jcarousel();
	// FancyBox jQuery
	jQuery("a.group").fancybox({ 'zoomSpeedIn': 300, 'zoomSpeedOut': 300, 'overlayShow': true }); 	
	// Slider Homepage
	jQuery('#slider').cycle({
        fx: 'fade',
        speed: 2000,
		timeout: 5000,
        pager: '#controls',
		slideExpr: '.panel'
    });
});
