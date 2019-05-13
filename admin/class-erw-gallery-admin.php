<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.tureyweb.com/
 * @since      1.0.0
 *
 * @package    Erw_Gallery
 * @subpackage Erw_Gallery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Erw_Gallery
 * @subpackage Erw_Gallery/admin
 * @author     TRW Team  <info@tureyweb.com>
 */
class Erw_Gallery_Admin {

	private $plugin_name;

	private $version;

	private $gallery_settings;





	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name      = $plugin_name;
		$this->version          = $version;
		$this->gallery_settings = unserialize(base64_decode(get_post_meta( $post_id['id'], 'erw_settings_'.$post_id['id'], true)));

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Erw_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Erw_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/erw-gallery-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Erw_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Erw_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_script('media-upload');
		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/erw-gallery-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function erw_register_post_gallery() {
		require_once( EG_INCLUDES . 'eg-post-gallery.php' );
	}

	public function _admin_add_meta_box() {
		// Syntax: add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
		add_meta_box('Add Image','Add Image',array(&$this,'erw_upload_multiple_images'),'erw_gallery','normal','default');
		add_meta_box('Shortcode Gallery','Shortcode Gallery',array(&$this,'meta_box_shortcode_gallery'),'erw_gallery','side','high' );
		add_meta_box('Gallery Setting','Gallery Setting',array(&$this,'erw_setting_gallery'),'erw_gallery','side','default' );
	}

	public function erw_upload_multiple_images($post) { ?>
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

	public function meta_box_shortcode_gallery($post) { ?> 
		<p class="input-text-wrap">
			<p>Copy & Embed shotcode into any Page/ Post / Text Widget to display your image gallery on site.</p>
			<input type="text" name="shortcode" id="shortcode" value="<?php echo "[erwgallery id=".$post->ID."]"; ?>" readonly />
		</p>
		<?php
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
			if ( is_array($image_ids)) {
				foreach($image_ids as $image_id) {
					$single_image_update = array(
						'ID'           => $image_id,
						'post_title'   => $image_titles[$i],
					);
					wp_update_post( $single_image_update );
					$i++;
				}
			}
			$erw_gallery_shortcode_setting = "erw_settings_".$post_id;
			update_post_meta($post_id, $erw_gallery_shortcode_setting, base64_encode(serialize($_POST)));
		}
	}// end save setting

	public function erw_setting_gallery($post) {
		require( EG_INCLUDES . 'eg-settings.php' );
	}

	public function register_shortcodes(){
		require( EG_INCLUDES . 'eg-shortcodes.php' );
	}
}
