<?php
/**
 * No Light Box Only Gallery
 */
$allslides = array(  'p' => $erw_gallery_id, 'post_type' => 'erw_gallery', 'orderby' => 'ASC');
$loop = new WP_Query( $allslides );
while ( $loop->have_posts() ) : $loop->the_post();

	$post_id = get_the_ID();
	$gallery_settings = unserialize(base64_decode(get_post_meta( $post_id, 'erw_settings_'.$post_id, true)));
	count($gallery_settings['slide-ids']);
	// start the image gallery contents
	?>
	<div id="erw_gallery_<?php echo $erw_gallery_id; ?>" class="row all-images">
		<?php
		if(isset($gallery_settings['slide-ids']) && count($gallery_settings['slide-ids']) > 0) {
			if($thumbnail_order == "DESC") {
				$gallery_settings['slide-ids'] = array_reverse($gallery_settings['slide-ids']);
			}
			if($thumbnail_order == "RANDOM") {
				shuffle($gallery_settings['slide-ids']);
			}

			foreach($gallery_settings['slide-ids'] as $attachment_id) {
				$thumb              = wp_get_attachment_image_src($attachment_id, 'thumb', true);
				$thumbnail          = wp_get_attachment_image_src($attachment_id, 'thumbnail', true);
				$medium             = wp_get_attachment_image_src($attachment_id, 'medium', true);
				$large              = wp_get_attachment_image_src($attachment_id, 'large', true);
				$full               = wp_get_attachment_image_src($attachment_id, 'full', true);
				$postthumbnail      = wp_get_attachment_image_src($attachment_id, 'post-thumbnail', true);
				$attachment_details = get_post( $attachment_id );
				$href               = get_permalink( $attachment_details->ID );
				$src                = $attachment_details->guid;
				$title              = $attachment_details->post_title;
				$description        = $attachment_details->post_content;
				if(isset($slidetext) == 'true') {
					if($slidetextopt == 'title') $text = $title;
				} else {
					$text = $title;
				}
				
				//set thumbnail size
				if($gal_thumb_size == "thumbnail") { $thumbnail_url = $thumbnail[0]; }
				if($gal_thumb_size == "medium") { $thumbnail_url = $medium[0]; }
				if($gal_thumb_size == "large") { $thumbnail_url = $large[0]; }
				if($gal_thumb_size == "full") { $thumbnail_url = $full[0]; }
					?>
					<div class="single-image <?php echo $col_large_desktops; ?> <?php echo $col_desktops; ?> <?php echo $col_tablets; ?> <?php echo $col_phones; ?>">
						<img class="thumbnail <?php echo $image_hover_effect; ?>" src="<?php echo $thumbnail_url; ?>" alt="<?php echo $title; ?>">
						<?php if($img_title == 0) { ?>
						<span class="item-title"><?php echo $title; ?></span>
						<?php } ?>
					</div>
					<?php
			}// end of attachment foreach
		} else {
			_e('Sorry! No image gallery found ', PLUGIN_TEXT_DOMAIN);
			echo ": [erwgallery id=$post_id]";
		} // end of if esle of slides avaialble check into slider
		?>
	</div>
<?php
endwhile;
wp_reset_query(); ?>
<script>
jQuery(document).ready(function () {
	var $grid = jQuery('.all-images').isotope({
		itemSelector: '.single-image',
	});
	$grid.imagesLoaded().progress( function() {
		$grid.isotope('layout');
	});
});
</script>