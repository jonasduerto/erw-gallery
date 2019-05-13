(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


	$(function() {
	    
	    var file_frame,
	    awl_erw_gallery = {
	        ul: '',
	        init: function() {
	            this.ul = jQuery('.sbox');
	            this.ul.sortable({
	                placeholder: '',
					revert: true,
	            });			
				
	            /**
				 * Add Slide Callback Funtion
				 */
	            jQuery('#add-new-slider').on('click', function(event) {
	                event.preventDefault();
	                if (file_frame) {
	                    file_frame.open();
	                    return;
	                }
	                file_frame = wp.media.frames.file_frame = wp.media({
	                    multiple: true
	                });

	                file_frame.on('select', function() {
	                    var images = file_frame.state().get('selection').toJSON(),
	                            length = images.length;
	                    for (var i = 0; i < length; i++) {
	                        awl_erw_gallery.get_thumbnail(images[i]['id']);
	                    }
	                });
	                file_frame.open();
	            });
				
				/**
				 * Delete Slide Callback Function
				 */
	            this.ul.on('click', '#remove-slide', function() {
	                if (confirm('Are sure to delete this images?')) {
	                    jQuery(this).parent().fadeOut(700, function() {
	                        jQuery(this).remove();
	                    });
	                }
	                return false;
	            });
				
				/**
				 * Delete All Slides Callback Function
				 */
				jQuery('#remove-all-slides').on('click', function() {
	                if (confirm('Are sure to delete all images?')) {
	                    awl_erw_gallery.ul.empty();
	                }
	                return false;
	            });
	           
	        },
	        get_thumbnail: function(id, cb) {
	            cb = cb || function() {
	            };
	            var data = {
	                action: 'erw_gallery_js',
	                slideId: id
	            };
	            jQuery.post(ajaxurl, data, function(response) {
	                awl_erw_gallery.ul.append(response);
	                cb();
	            });
	        }
	    };
	    awl_erw_gallery.init();
	});

})( jQuery );
