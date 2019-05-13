<?php


function _get_all_image_sizes() {
    global $_wp_additional_image_sizes;

    $default_image_sizes = get_intermediate_image_sizes();

    foreach ( $default_image_sizes as $size ) {
        $image_sizes[ $size ][ 'width' ] = intval( get_option( "{$size}_size_w" ) );
        $image_sizes[ $size ][ 'height' ] = intval( get_option( "{$size}_size_h" ) );
        $image_sizes[ $size ][ 'crop' ] = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
    }

    if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
        $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
    }

    return $image_sizes;
}





//load settings
$gallery_settings = unserialize(base64_decode(get_post_meta( $post->ID, 'erw_settings_'.$post->ID, true)));
//print_r($gallery_settings);
$erw_gallery_id = $post->ID; ?>

	<div class="erwg_settings">
		<p class="bg-title">Style</p></br>
		<?php if(isset($gallery_settings['style-type'])) $style_type = $gallery_settings['style-type']; else $style_type = 6; ?>
		<select name="style-type" id="style-type">
			<option value="6" <?php if($style_type == 6) echo "selected=selected"; ?>>Grid</option>
			<option value="9" <?php if($style_type == 9) echo "selected=selected"; ?>>Carousel</option>
		</select>
		<h4>Select a Style Type</h4>
	</div>

	<div class="erwg_settings">
		<p class="bg-title">Image Hover Effect Type</p></br>
		<div class="switch-field em_size_field">
			<?php if(isset($gallery_settings['image_hover_effect_type'])) $image_hover_effect_type = $gallery_settings['image_hover_effect_type']; else $image_hover_effect_type = "sg"; ?>
			<input type="radio" name="image_hover_effect_type" id="image_hover_effect_type1" value="no" <?php if($image_hover_effect_type == "no") echo "checked=checked"; ?>>
			<label for="image_hover_effect_type1">None</label>
			<input type="radio" name="image_hover_effect_type" id="image_hover_effect_type2" value="sg" <?php if($image_hover_effect_type == "sg") echo "checked=checked"; ?>>
			<label for="image_hover_effect_type2">2D</label>
		</div>
		<h4>Select and Set a image hover effect type for Gallery<h4>

		<!-- 4 -->
		<div class="he_four erwg_settings">
			<label>Image Hover Effects</label><br>
			<?php if(isset($gallery_settings['image_hover_effect_four'])) $image_hover_effect_four = $gallery_settings['image_hover_effect_four']; else $image_hover_effect_four = "hvr-glow"; ?>
			<select name="image_hover_effect_four" id="image_hover_effect_four">
				<optgroup label="Shadow and Glow Transitions Effects" class="sg">
					<option value="hvr-grow-shadow" <?php if($image_hover_effect_four == "hvr-grow-shadow") echo "selected=selected"; ?>>Grow Shadow</option>
					<option value="hvr-float-shadow" <?php if($image_hover_effect_four == "hvr-float-shadow") echo "selected=selected"; ?>>Float Shadow</option>
					<option value="hvr-glow" <?php if($image_hover_effect_four == "hvr-glow") echo "selected=selected"; ?>>Glow</option>
					<option value="hvr-box-shadow-inset" <?php if($image_hover_effect_four == "hvr-box-shadow-inset") echo "selected=selected"; ?>>Box-Shadow-Inset</option>
					<option value="hvr-box-shadow-outset" <?php if($image_hover_effect_four == "hvr-box-shadow-outset") echo "selected=selected"; ?>>Box Shadow Outset</option>
				</optgroup>
			</select>
		</div>
	</div><br>

	<div class="erwg_settings">
		<p class="bg-title">Light Box Style</p></br>
		<?php if(isset($gallery_settings['light-box'])) $light_box = $gallery_settings['light-box']; else $light_box = 1; ?>
		<select name="light-box" id="light-box">	
			<option value="0" <?php if($light_box == 0) echo "selected=selected"; ?>>None</option>
			<option value="6" <?php if($light_box == 6) echo "selected=selected"; ?>>Bootstrap Light Box</option>
			<option value="9" <?php if($light_box == 9) echo "selected=selected"; ?>>Fancybox Light box</option>
		</select>
		<h4>Select a light box style</h4>
	</div>

	<div class="erwg_settings">
		<p class="bg-title">Gallery Thumbnail Size</p></br>
		<?php 
		global $_wp_additional_image_sizes; 

		if(isset($gallery_settings['gal_thumb_size'])) $gal_thumb_size = $gallery_settings['gal_thumb_size']; else $gal_thumb_size = "thumbnail"; 

		// echo '<pre>';
		// print_r($gallery_settings['gal_thumb_size']);
		// print_r($gal_thumb_size);
		// echo '</pre>';
	 
	    $available_size  = '<select id="gal_thumb_size" name="gal_thumb_size" class="form-control">' . "\r\n";
	    $available_size .= '<option value="0" selected="selected">Choose</option>' . "\r\n";
	    foreach ($_wp_additional_image_sizes as $size_name => $size_attrs) {

	        if ( $gal_thumb_size == $size_name ) :
	            $available_size .= '<option value="' . $size_name . '" selected="selected">' . $size_name . ' – ' . $size_attrs['width'] . '×' . $size_attrs['height'] . ' | ' . $size_attrs['crop'] .'</option>' . "\r\n";
	        else :
	            $available_size .= '<option value="' . $size_name . '">' . $size_name . ' – ' . $size_attrs['width'] . '×' . $size_attrs['height'] . ' | ' . $size_attrs['crop'] .'</option>' . "\r\n";         
	        endif;
	    }

	    $available_size .= '</select>' . "\r\n";

	    echo $available_size;

		?>
		<h4>Select gallery thumnails size to display into gallery</h4>
	</div>

	<div class="erwg_settings">
		<p class="bg-title">Hide Thumbnails Title</p>
		<p class=" switch-field em_size_field">
			<?php if(isset($gallery_settings['img_title'])) $img_title = $gallery_settings['img_title']; else $img_title = 0; ?>
			<input type="radio" name="img_title" id="img_title1" value="1" <?php if($img_title == 1) echo "checked=checked"; ?>>
			<label for="img_title1">Yes</label>
			<input type="radio" name="img_title" id="img_title2" value="0" <?php if($img_title == 0) echo "checked=checked"; ?>>
			<label for="img_title2">No</label>
		</p>
	</div>

	<div class="erwg_settings">
		<p class="bg-title">Gallery Thumbnail Order</p>
		<p class="switch-field em_size_field">	
			<?php if(isset($gallery_settings['thumbnail_order'])) $thumbnail_order = $gallery_settings['thumbnail_order']; else $thumbnail_order = "ASC"; ?>
			<input type="radio" name="thumbnail_order" id="thumbnail_order1" value="ASC" <?php if($thumbnail_order == "ASC") echo "checked=checked"; ?>>
			<label for="thumbnail_order1">Old First</label>
			<input type="radio" name="thumbnail_order" id="thumbnail_order2" value="DESC" <?php if($thumbnail_order == "DESC") echo "checked=checked"; ?>>
			<label for="thumbnail_order2">New First</label>
			<input type="radio" name="thumbnail_order" id="thumbnail_order3" value="RANDOM" <?php if($thumbnail_order == "RANDOM") echo "checked=checked"; ?>>
			<label for="thumbnail_order3">Random</label>
			<h4>Set a image order in which you want to display gallery thumbnails</h4>
		</p>
	</div>

	<div class="erwg_settings">
		<p class="bg-title">Custom CSS</p>
		<?php if(isset($gallery_settings['custom-css'])) $custom_css = $gallery_settings['custom-css']; else $custom_css = ""; ?>
		
		<h4>Apply own css on image gallery and dont use style tag</h4>
		<textarea name="custom-css" id="custom-css" style="width: 100%; height: 150px;" placeholder="Type direct CSS code here. Don't use <style>...</style> tag."><?php echo $custom_css; ?></textarea>
	</div>

	<input type="hidden" name="erw-settings" id="erw-settings" value="erw-save-settings">

<!-- Return to Top -->
<a href="javascript:" id="return-to-top"><i class="fa fa-chevron-up"></i></a>

<script>
// ===== Scroll to Top ==== 
jQuery(window).scroll(function() {
    if (jQuery(this).scrollTop() >= 50) {        // If page is scrolled more than 50px
        jQuery('#return-to-top').fadeIn(200);    // Fade in the arrow
    } else {
        jQuery('#return-to-top').fadeOut(200);   // Else fade out the arrow
    }
});
jQuery('#return-to-top').click(function() {      // When arrow is clicked
    jQuery('body,html').animate({
        scrollTop : 0                       // Scroll to top of body
    }, 500);
});

var effect_type = jQuery('input[name="image_hover_effect_type"]:checked').val();
//alert(effect_type);
if(effect_type == "no") {
	jQuery('.he_one').hide();
	jQuery('.he_two').hide();
	jQuery('.he_three').hide();
	jQuery('.he_four').hide();
	jQuery('.he_five').hide();
	jQuery('.image-hover-color').show();
	jQuery('.title-bg-color').show();
}
if(effect_type == "sg") {
	jQuery('.he_one').hide();
	jQuery('.he_two').hide();
	jQuery('.he_three').hide();
	jQuery('.he_four').show();
	jQuery('.he_five').hide();
	jQuery('.title-bg-color').show();
	jQuery('.image-hover-color').show();
}

//on change effect
jQuery(document).ready(function() {
	jQuery('input[name="image_hover_effect_type"]').change(function(){
		var effect_type = jQuery('input[name="image_hover_effect_type"]:checked').val();
		if(effect_type == "no") {
			jQuery('.he_one').hide();
			jQuery('.he_two').hide();
			jQuery('.he_three').hide();
			jQuery('.he_four').hide();
			jQuery('.he_five').hide();
			jQuery('.title-bg-color').hide();
			jQuery('.image-hover-color').hide();
		}
		if(effect_type == "sg") {
			jQuery('.he_one').hide();
			jQuery('.he_two').hide();
			jQuery('.he_three').hide();
			jQuery('.he_four').show();
			jQuery('.he_five').hide();
			jQuery('.title-bg-color').show();
			jQuery('.image-hover-color').show();
		}
	});
});
</script>