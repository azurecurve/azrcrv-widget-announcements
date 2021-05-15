/*
 * Tabs
 */
jQuery(function($) {
	'use strict';
	
	$('#tabs ul li a').on('keyup click', function(e) {
        if (e.key === 'Enter' || e.type === 'click') {
			var id = $(this).attr('href');
			$('.ui-state-active').removeClass('ui-state-active').attr('aria-selected', 'false').attr('aria-expanded', 'false');
			$(this).parent('li').addClass('ui-state-active').attr('aria-selected', 'true').attr('aria-expanded', 'true');
			$(this).closest('ul').siblings().addClass('ui-tabs-hidden').attr('aria-hidden', 'true');
			$(id).removeClass('ui-tabs-hidden').attr('aria-hidden', 'false');
			e.preventDefault();
		}
	});
	
	$('#azrcrv-tabs ul li a').hover(
		function() { $(this).addClass('ui-state-hover'); },
		function() { $(this).removeClass('ui-state-hover'); }
	);
});


/*
 * Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
 *
 * Image 1
 */
jQuery(document).ready(function($){  
	// Uploading files
	var file_frame;
	
	$('#azrcrv-wa-upload-image-1').on('click', function( event ){
 
		event.preventDefault();
	 
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}
	 
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
			button: {
				text: $( this ).data( 'uploader_button_text' ),
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
	 
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();

			// Do something with attachment.id and/or attachment.url here
			jQuery('#tweet-image-1').attr('src',attachment.url);
			jQuery('#tweet-selected-image-1').attr('value',attachment.url);
		});
	 
		// Finally, open the modal
		file_frame.open();
	});
	$('#azrcrv-wa-remove-image-1').on('click', function( event ){
 
		// remove image and url
		jQuery('#tweet-image-1').attr('src','');
		jQuery('#tweet-selected-image-1').attr('value','');
		
	});
});


/*
 * Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
 *
 * Image 2
 */
jQuery(document).ready(function($){  
	// Uploading files
	var file_frame;
	
	$('#azrcrv-wa-upload-image-2').on('click', function( event ){
 
		event.preventDefault();
	 
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}
	 
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
			button: {
				text: $( this ).data( 'uploader_button_text' ),
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
	 
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();

			// Do something with attachment.id and/or attachment.url here
			jQuery('#tweet-image-2').attr('src',attachment.url);
			jQuery('#tweet-selected-image-2').attr('value',attachment.url);
		});
	 
		// Finally, open the modal
		file_frame.open();
	});
	$('#azrcrv-wa-remove-image-2').on('click', function( event ){
 
		// remove image and url
		jQuery('#tweet-image-2').attr('src','');
		jQuery('#tweet-selected-image-2').attr('value','');
		
	});
});


/*
 * Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
 *
 * Image 3
 */
jQuery(document).ready(function($){  
	// Uploading files
	var file_frame;
	
	$('#azrcrv-wa-upload-image-3').on('click', function( event ){
 
		event.preventDefault();
	 
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}
	 
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
			button: {
				text: $( this ).data( 'uploader_button_text' ),
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
	 
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();

			// Do something with attachment.id and/or attachment.url here
			jQuery('#tweet-image-3').attr('src',attachment.url);
			jQuery('#tweet-selected-image-3').attr('value',attachment.url);
		});
	 
		// Finally, open the modal
		file_frame.open();
	});
	$('#azrcrv-wa-remove-image-3').on('click', function( event ){
 
		// remove image and url
		jQuery('#tweet-image-3').attr('src','');
		jQuery('#tweet-selected-image-3').attr('value','');
		
	});
});


/*
 * Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
 *
 * Image 4
 */
jQuery(document).ready(function($){  
	// Uploading files
	var file_frame;
	
	$('#azrcrv-wa-upload-image-4').on('click', function( event ){
 
		event.preventDefault();
	 
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}
	 
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
			button: {
				text: $( this ).data( 'uploader_button_text' ),
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
	 
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();

			// Do something with attachment.id and/or attachment.url here
			jQuery('#tweet-image-4').attr('src',attachment.url);
			jQuery('#tweet-selected-image-4').attr('value',attachment.url);
		});
	 
		// Finally, open the modal
		file_frame.open();
	});
	$('#azrcrv-wa-remove-image-4').on('click', function( event ){
 
		// remove image and url
		jQuery('#tweet-image-4').attr('src','');
		jQuery('#tweet-selected-image-4').attr('value','');
		
	});
});
