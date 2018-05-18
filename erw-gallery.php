<?php
/**

Plugin Name: ElReyWeb's Gallery
Plugin URI: http://elreyweb.com/
Description: ElReyWeb's Gallery is a plugin with lightbox preview for Wordpress
Version: 0.1.2
Author: ElReyWeb's Team
Author URI: http://elreyweb.com/
License: GPLv2 or later
Text Domain: PLUGIN_TEXT_DOMAIN
Domain Path: /languages
*/

if ( ! class_exists( 'erw_gallery_class' ) ) {

	class erw_gallery_class {
		
		protected $protected_plugin_api;
		protected $ajax_plugin_nonce;
		
		public function __construct() {
			$this->_constants();
			$this->_hooks();
		}
		
		protected function _constants() {
			//Plugin Version
			define( 'PLUGIN_VER', '0.1.2' );
			
			//Plugin Text Domain
			define("PLUGIN_TEXT_DOMAIN", "erw-gallery" );
			//Plugin Name
			define( 'PLUGIN_NAME', "ElReyWeb's Gallery" );
			//Plugin Slug
			define( 'PLUGIN_SLUG', 'erw_gallery' );
			//Plugin Directory Path
			define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			//Plugin Directory URL
			define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );

			/**
			 * Create a key for the .htaccess secure download link.
			 * @uses    NONCE_KEY     Defined in the WP root config.php
			 */
			define( 'ERW_SECURE_KEY', md5( NONCE_KEY ) );
			
		} // end of constructor function
		
		protected function _hooks() {			
			//Create Image Gallery Custom Post
			add_action( 'init', array( $this, 'erw_register_post_gallery' ));
			
			//Add meta box to custom post
			add_action( 'add_meta_boxes', array( $this, '_admin_add_meta_box' ) );
			 
			//loaded during admin init 
			add_action( 'admin_init', array( $this, '_admin_add_meta_box' ) );
			
			add_action('wp_ajax_erw_gallery_js', array(&$this, '_ajax_erw_gallery'));
		
			add_action('save_post', array(&$this, '_erw_save_settings'));

			//Shortcode Compatibility in Text Widgets
			add_filter('widget_text', 'do_shortcode');

		}

		public function erw_register_post_gallery() {
			$labels = array(
				'name'                => _x( 'Gallery', 'Post Type General Name', 'PLUGIN_TEXT_DOMAIN' ),
				'singular_name'       => _x( 'ERW Gallery', 'Post Type Singular Name', 'PLUGIN_TEXT_DOMAIN' ),
				'menu_name'           => __( 'Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'name_admin_bar'      => __( 'ERW Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'parent_item_colon'   => __( 'Parent Item:', 'PLUGIN_TEXT_DOMAIN' ),
				'all_items'           => __( 'All Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'add_new_item'        => __( 'Add New Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'add_new'             => __( 'Add Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'new_item'            => __( 'New Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'edit_item'           => __( 'Edit Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'update_item'         => __( 'Update Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'search_items'        => __( 'Search Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'not_found'           => __( 'Gallery Not found', 'PLUGIN_TEXT_DOMAIN' ),
				'not_found_in_trash'  => __( 'Gallery Not found in Trash', 'PLUGIN_TEXT_DOMAIN' ),
			);
			$args = array(
				'label'               => __( 'ERW Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'description'         => __( 'Custom Post Type For Gallery', 'PLUGIN_TEXT_DOMAIN' ),
				'labels'              => $labels,
				'supports'            => array( 'title'),
				'taxonomies'          => array(),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 20,
				'menu_icon'           => 'dashicons-images-alt2',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,		
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
			);
			register_post_type( 'erw_gallery', $args );
		} 
		public function _admin_add_meta_box() {
			// Syntax: add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
			add_meta_box('Add Image','Add Image',array(&$this,'erw_upload_multiple_images'),'erw_gallery','normal','default');
			add_meta_box('Shortcode Gallery','Shortcode Gallery',array(&$this,'erw_shortcode_gallery'),'erw_gallery','side','high' );
			add_meta_box('Gallery Setting','Gallery Setting',array(&$this,'erw_setting_gallery'),'erw_gallery','side','default' );
		}
		public function erw_upload_multiple_images($post) { 
				wp_enqueue_script('media-upload');
				wp_enqueue_script('erw_uploader-js', PLUGIN_URL . 'assets/js/uploader.js', array('jquery'));
				// wp_enqueue_style('erwg_uploader-css', PLUGIN_URL . 'assets/css/uploader.css');
				wp_enqueue_media();
				// wp_enqueue_style( 'wp-color-picker' );
				// wp_enqueue_script( 'erw-color-picker-js', plugins_url('assets/js/color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
				?>
				<div id="slider-gallery">
					<input type="button" id="remove-all-slides" name="remove-all-slides" class="button button-large remove-all-slides" value="Delete All Images" />
					<input type="button" id="add-new-slider" name="add-new-slider" class="button button-large new-slider" value="Add Image" />
					<ul id="remove-slides" class="sbox">
					<?php
					$allimagesetting = unserialize(base64_decode(get_post_meta( $post->ID, 'erw_settings_'.$post->ID, true)));
					if(isset($allimagesetting['slide-ids'])) {
						$count = 0;
					foreach($allimagesetting['slide-ids'] as $id) {
						$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
						$attachment = get_post( $id );
						?>
						<li class="slide">
							<img class="new-slide" src="<?php echo $thumbnail[0]; ?>" alt="<?php echo get_the_title($id); ?>" style="height: 150px; width: 98%;">
							<input type="hidden" id="slide-ids[]" name="slide-ids[]" value="<?php echo $id; ?>" />
							<!-- Image Title, Caption, Alt Text, Description-->
							<!-- <input type="text" name="slide-title[]" id="slide-title[]" style="width: 100%;" placeholder="Image Title" value="<?php //echo get_the_title($id); ?>"> -->
							<!--<textarea name="slide-desc[]" id="slide-desc[]" style="width: 280px;" placeholder="Image Description" style="height: 120px; width: 280px; border-radius: 8px;"><?php echo $attachment->post_content; ?></textarea>-->
							<!--<input type="text" name="slide-link[]" id="slide-link[]" style="width: 100%;" placeholder="Image Link URL" value="<?php //echo $image_link; ?>">-->
							<input type="button" name="remove-slide" id="remove-slide" class="button remove-single-slide button-danger" value="&times;">
						</li>
					<?php $count++; } // end of foreach
					} //end of if
					?>
					</ul>
				</div>
			<?php 
		}

		public function erw_shortcode_gallery($post) { ?>			
			<p class="input-text-wrap">
				<p>Copy & Embed shotcode into any Page/ Post / Text Widget to display your image gallery on site.</p>
				<input type="text" name="shortcode" id="shortcode" value="<?php echo "[erwgallery id=".$post->ID."]"; ?>" readonly />
			</p>
			<?php
		}
		public function erw_setting_gallery($post) {
			require_once('gallery-settings.php');
		}

		public function _ig_ajax_callback_function($id) {
			//wp_get_attachment_image_src ( int $attachment_id, string|array $size = 'thumbnail', bool $icon = false )
			//thumb, thumbnail, medium, large, post-thumbnail
			$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
			$attachment = get_post( $id ); // $id = attachment id
			?>
			<li class="slide">
				<img class="new-slide" src="<?php echo $thumbnail[0]; ?>" alt="<?php echo get_the_title($id); ?>" style="height: 150px; width: 98%;">
				<input type="hidden" id="slide-ids[]" name="slide-ids[]" value="<?php echo $id; ?>" />
				<!-- <input type="text" name="slide-title[]" id="slide-title[]" style="width: 100%;" placeholder="Image Title" value="<?php echo get_the_title($id); ?>"> -->
				<!--<textarea name="slide-desc[]" id="slide-desc[]" placeholder="Image Description" style="height: 120px; width: 280px;"><?php echo $attachment->post_content; ?></textarea>-->
				<!--<input type="text" name="slide-link[]" id="slide-link[]" style="width: 100%;" placeholder="Image Link URL">-->
				<input type="button" name="remove-slide" id="remove-slide" style="width: 100%;" class="button" value="&times;">
			</li>
			<?php
		}
		
		public function _ajax_erw_gallery() {
			echo $this->_ig_ajax_callback_function($_POST['slideId']);
			die;
		}
		public function _erw_save_settings($post_id) {
			if ( isset( $_POST['erw-settings'] ) == "erw-save-settings" ) {
				$image_ids = $_POST['slide-ids'];
				$image_titles = $_POST['slide-title'];
				$i = 0;
				foreach($image_ids as $image_id) {
					$single_image_update = array(
						'ID'           => $image_id,
						'post_title'   => $image_titles[$i],
					);
					wp_update_post( $single_image_update );
					$i++;
				}				
				$erw_gallery_shortcode_setting = "erw_settings_".$post_id;
				update_post_meta($post_id, $erw_gallery_shortcode_setting, base64_encode(serialize($_POST)));
			}
		}// end save setting
				
	} // end of class


	$erw_gallery_object = new erw_gallery_class();

	add_shortcode('erwgallery', function ($post_id) {
		ob_start();
		//js
		wp_enqueue_script('jquery');
		wp_enqueue_script('imagesloaded-pkgd-js', PLUGIN_URL .'js/imagesloaded.pkgd.js', array('jquery'), '' , true);
		wp_enqueue_script('isotope-js', PLUGIN_URL .'js/isotope.pkgd.min.js', array('jquery'), '', false);
		
		$gallery_settings = unserialize(base64_decode(get_post_meta( $post_id['id'], 'erw_settings_'.$post_id['id'], true)));
		//print_r($gallery_settings);
		
		$erw_gallery_id = $post_id['id'];
		
		//columns settings
		$gal_thumb_size     = $gallery_settings['gal_thumb_size'];
		$col_large_desktops = $gallery_settings['col_large_desktops'];
		$col_desktops       = $gallery_settings['col_desktops'];
		$col_tablets        = $gallery_settings['col_tablets'];
		$col_phones         = $gallery_settings['col_phones'];
		
		// ligtbox style
		if(isset($gallery_settings['light-box'])) $light_box = $gallery_settings['light-box']; else $light_box = 1;
		
		//hover effect
		if(isset($gallery_settings['image_hover_effect_type'])) $image_hover_effect_type = $gallery_settings['image_hover_effect_type']; else $image_hover_effect_type = "no";
		if($image_hover_effect_type == "no") {
			$image_hover_effect = "";
		} else {
			// hover csss
			wp_enqueue_style('ggp-hover-css', PLUGIN_URL .'css/hover.css');
		}
		if($image_hover_effect_type == "sg")
			if(isset($gallery_settings['image_hover_effect_four'])) $image_hover_effect = $gallery_settings['image_hover_effect_four']; else $image_hover_effect = "hvr-box-shadow-outset";
		
		if(isset($gallery_settings['no_spacing'])) $no_spacing = $gallery_settings['no_spacing']; else $no_spacing = 1;
		if(isset($gallery_settings['thumbnail_order'])) $thumbnail_order = $gallery_settings['thumbnail_order']; else $thumbnail_order = "ASC";
		if(isset($gallery_settings['url_target'])) $url_target = $gallery_settings['url_target']; else $url_target = "_new";
		if(isset($gallery_settings['custom-css'])) $custom_css = $gallery_settings['custom-css']; else $custom_css = "";
		if(isset($gallery_settings['img_title'])) $img_title = $gallery_settings['img_title']; else $img_title = 0;
		?>
		<!-- CSS Part Start From Here-->
		<style>
		.all-images {
			padding-top: 10px !important;
			padding-bottom: 15px !important;
		}
		#erw_gallery_<?php echo $erw_gallery_id; ?> .thumbnail {
			width: 100% !important;
			height: auto !important;
			border-radius: 0px;
			/*background: transparent url('<?php echo PLUGIN_URL.'img/loading.gif'; ?>') center no-repeat !important;*/
		}
		<?php if($no_spacing) { ?>
		#erw_gallery_<?php echo $erw_gallery_id; ?> .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
			padding-right: 0px !important;
			padding-left: 0px !important;
		}
		#erw_gallery_<?php echo $erw_gallery_id; ?> .thumbnail {
			padding: 0px !important;
			margin-bottom: 0px !important;
			border: 0px !important;
		}
		<?php } ?>
		.item-title {
			background-color: rgba(0, 0, 0, 0.5);
			bottom: 45px;
			color: #FFFFFF;
			display: block;
			font-weight: 300;
			left: 2rem;
			padding: 8px;
			position: absolute;
			right: 2rem;
			text-align: center;
			text-transform: capitalize;
		}
		<?php echo $custom_css; ?>
		</style>
		<?php if($light_box == 0) {
			require('no-lightbox.php');
		}
		if($light_box == 6) {
			require('bootstrap-lightbox.php');
		}
		return ob_get_clean();
	});























} // end of class exists
?>