jQuery(document).ready(function($) {

	$('img[usemap]').rwdImageMaps();
	
	svgeezy.init(false, 'png');

	$('.match-all').matchHeight({ byRow: false });
	
	// Setup the mobile menu
	$('#nav .toggle').on('click', function(e) {
		e.preventDefault();
		if( $('#mobile-nav .menu').length === 0) {
			var $nav = $('#nav .menu').clone();
			$('#mobile-nav header').after($nav);
		}
		$('#mobile-nav').show();
	});
	
	// Open mobil menu
	$('#mobile-nav .close').live('click', function(e) {
		e.preventDefault();
		$('#mobile-nav').hide();
	});
	
	// Clear & reset form fields on foucs & blur
	if( $('.form-block').length !== 0) {
		$('.form-block form ul input').on('focus', function() {
			var label = $(this).prev('label').text();
			if($(this).val() == label) $(this).val('');
		});
		$('.form-block form ul input').on('blur', function() {
			var label = $(this).prev('label').text();
			if($(this).val() == '') $(this).val(label);
		});
	}
	
	// Clear & reset form fields on foucs & blur
	$('.clear-field').on('focus', function() {
		var label = $(this).prev('label').text();
		if($(this).val() == label) $(this).val('');
	});
	$('.clear-field').on('blur', function() {
		var label = $(this).prev('label').text();
		if($(this).val() == '') $(this).val(label);
	});
	
	// Equal height grid items
	if( $('.partners .item').length !== 0 ) {
		$('.partners .item').matchHeight();
	}
	
	// Equal height list items
	if( $('.tweet-wrap li').length !== 0 ) {
		if( $('.tweet-wrap li iframe').length !== 0 ) {
			$('.tweet-wrap li:first-child iframe').load( function() {
				$('.tweet-wrap li').matchHeight();
			});
		} else {
			$('.tweet-wrap li').matchHeight();
		}
		
	}
	
	// Lightboxes
	$(".fancybox").fancybox();
	
	// Lightboxes
	$(".inline-box").fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: true,
		autoSize	: true,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});

	// Display enterprise price levels
	$('#plan-select').on('change', function() {
		$('.price-plan .price .level').hide();
		$('.price-plan .price .level').eq($(this).val()-1).show();
	});

});

