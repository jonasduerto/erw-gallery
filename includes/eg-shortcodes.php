<?php 
/*
*
*	***** ERW Gallery *****
*
*	Shortcodes
*	
*/
// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if

/*
*
*  Build The Custom Plugin Form
*
*  Display Anywhere Using Shortcode: [eg_gallery]
*
*/

add_shortcode('erwgallery', function($post_id) {
    ob_start();
      
    $gallery_settings = unserialize(base64_decode(get_post_meta( $post_id['id'], 'erw_settings_'.$post_id['id'], true)));
    //print_r($gallery_settings);
    
    $erw_gallery_id = $post_id['id'];
    
    //columns settings
    $gal_thumb_size     = $gallery_settings['gal_thumb_size'];
    
    // ligtbox style
    if(isset($gallery_settings['light-box'])) $light_box = $gallery_settings['light-box']; else $light_box = 1;

    // Type style
    if(isset($gallery_settings['style-type'])) $style_type = $gallery_settings['style-type']; else $style_type = 1;
    
    //hover effect
    if(isset($gallery_settings['image_hover_effect_type'])) $image_hover_effect_type = $gallery_settings['image_hover_effect_type']; else $image_hover_effect_type = "no";
    if($image_hover_effect_type == "no") {
      $image_hover_effect = "";
    } else {
      // hover csss
      wp_enqueue_style('erw-hover-css', EG_PUBLIC_CSS . 'hover.css');
    }
    if($image_hover_effect_type == "sg")
      if(isset($gallery_settings['image_hover_effect_four'])) $image_hover_effect = $gallery_settings['image_hover_effect_four']; else $image_hover_effect = "hvr-box-shadow-outset";
    
    if(isset($gallery_settings['no_spacing'])) $no_spacing = $gallery_settings['no_spacing']; else $no_spacing = 1;
    if(isset($gallery_settings['thumbnail_order'])) $thumbnail_order = $gallery_settings['thumbnail_order']; else $thumbnail_order = "ASC";
    if(isset($gallery_settings['url_target'])) $url_target = $gallery_settings['url_target']; else $url_target = "_new";
    if(isset($gallery_settings['custom-css'])) $custom_css = $gallery_settings['custom-css']; else $custom_css = "";
    if(isset($gallery_settings['img_title'])) $img_title = $gallery_settings['img_title']; else $img_title = 0;
    
    $arg = array(  
        'p'         => $erw_gallery_id, 
        'post_type' => 'erw_gallery', 
        'orderby'   => 'ASC'
    );
    $query = new WP_Query( $arg );
    if (!$query->have_posts()) {

           return 'Empty result';

    } else {

        wp_enqueue_script('jquery');
        wp_enqueue_script('imagesloaded-pkgd-js', EG_PUBLIC_JS . 'imagesloaded.pkgd.js', array('jquery'), '' , true);
        wp_enqueue_script('isotope-js', EG_PUBLIC_JS . 'isotope.pkgd.min.js', array('jquery'), '', false);

        if($style_type == 9) {
            wp_enqueue_style('eg-owl-carousel-css', EG_PUBLIC_CSS .'jquery.owl-carousel.min.css');
            wp_enqueue_script('eg-owl-carousel-js', EG_PUBLIC_JS .'jquery.owl-carousel.min.js', array('jquery'), '' , true);
        } 
        if( $light_box == 9 ) {
            wp_enqueue_style('eg-fancybox-css', EG_PUBLIC_CSS .'jquery.fancybox.min.css');
            wp_enqueue_script('eg-fancybox-js', EG_PUBLIC_JS .'jquery.fancybox.min.js', array('jquery'), '' , true);
        }
        if( $light_box == 6 ) {
            wp_enqueue_style('eg-lightbox-css', EG_PUBLIC_CSS .'ekko-lightbox.css');
            wp_enqueue_script('eg-lightbox-js', EG_PUBLIC_JS .'ekko-lightbox.js', array('jquery'), '' , true);
        }
        
        $output = "";
        $lightbox_attr = '';

        while ( $query->have_posts() ) : $query->the_post();

            $post_id = get_the_ID();
            $gallery_settings = unserialize(base64_decode(get_post_meta( $post_id, 'erw_settings_'.$post_id, true)));
            count($gallery_settings['slide-ids']);
            // start the image gallery contents
            ?>
            <div id="erw_gallery_<?php echo $erw_gallery_id; ?>" class="row all-images">
                <?php if(isset($gallery_settings['slide-ids']) && count($gallery_settings['slide-ids']) > 0) {
                    
                    if($thumbnail_order == "DESC") {
                        $gallery_settings['slide-ids'] = array_reverse($gallery_settings['slide-ids']);
                    }
                    if($thumbnail_order == "RANDOM") {
                        shuffle($gallery_settings['slide-ids']);
                    }
                    foreach($gallery_settings['slide-ids'] as $attachment_id) {             
                        $thumbnail          = wp_get_attachment_image_src($attachment_id, $gal_thumb_size, true);
                        $full               = wp_get_attachment_image_src($attachment_id, 'full', true);
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
                        
                        if($style_type == 9) {
                            // owl-carousel
                            $lightbox_attr = 'data-fancybox="gallery-'. $erw_gallery_id. '" data-caption="'. $title. '" href="'. $full[0]. '" data-type="image"';
                        }
                        if( ($style_type == 6) && ($light_box == 6) ) {
                            // bootstrap-lightbox
                            $lightbox_attr = 'data-toggle="lightbox" data-gallery="gallery-'. $erw_gallery_id. '" data-title="'. $title. '" href="'. $full[0]. '" data-type="image"';
                            $col_class = $col_large_desktops .' '. $col_desktops .' '. $col_tablets .' '. $col_phones;
                        }
                        if( ($style_type == 6) && ($light_box == 9) ) {
                            // fancybox_lightbox
                            $lightbox_attr = 'data-fancybox="gallery-'. $erw_gallery_id. '" data-caption="'. $title. '" href="'. $full[0]. '" data-type="image"';
                            $col_class = $col_large_desktops .' '. $col_desktops .' '. $col_tablets .' '. $col_phones;
                        }

                        echo "<div class='single-image $col_class'>";
                        
                        if ($light_box != 0) { echo "<a $lightbox_attr>"; }

                        echo "<img class='thumbnail $image_hover_effect' src='$thumbnail[0]' width='$thumbnail[1]' height='$thumbnail[2]' alt='$title'>";

                        if ($img_title == 0) { echo "<span class='item-title'>$title</span>"; }

                        if ($light_box != 0) { echo "</a>"; }

                        echo "</div>";
                    }// end of attachment foreach
                } else {
                    _e('Sorry! No image gallery found ', PLUGIN_TEXT_DOMAIN);
                    echo ": [erwgallery id=$post_id]";
                } // end of if esle of slides avaialble check into slider
                ?>
            </div>
        <?php
        endwhile;
        wp_reset_query();

        if ($style_type != 9): ?>
            <style>
                .all-images {
                    padding-top: 10px;
                    padding-bottom: 15px;
                }
                #erw_gallery_<?php echo $erw_gallery_id; ?> .thumbnail {
                    width: 100% !important;
                    height: auto !important;
                    border-radius: 0px;
                    background: transparent url('<?php echo EG_PUBLIC_IMG.'img/loading.gif'; ?>') center no-repeat !important;
                }

                <?php if ($no_spacing): ?>
                    #erw_gallery_<?php echo $erw_gallery_id; ?> .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
                      padding-right: 0px !important;
                      padding-left: 0px !important;
                    }
                    #erw_gallery_<?php echo $erw_gallery_id; ?> .thumbnail {
                      padding: 0px !important;
                      margin-bottom: 0px !important;
                      border: 0px !important;
                    }
                <?php endif ?>
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
        <?php endif ?>

        <script type="text/javascript">
            <?php if ($style_type == 9): //owl-carousel ?>
                jQuery(document).ready(function () {

                    $('[data-fancybox="gallery-<?php echo $erw_gallery_id; ?>"]').fancybox({
                        protect: true
                    });

                    var product_owl_<?php echo $erw_gallery_id; ?> = $('#erw_gallery_<?php echo $erw_gallery_id; ?> .owl-carousel');
                    func_owl_nav('#erw_gallery_<?php echo $erw_gallery_id; ?> .owl-carousel');
                    product_owl_<?php echo $erw_gallery_id; ?>.owlCarousel({
                        items               :8,
                        loop                :true,
                        // nav                 :true,
                        margin              :5,
                        autoplay            :true,
                        animateOut          :'bounceOut',
                        autoplayTimeout     :4000,
                        autoplayHoverPause  :true,
                        responsiveClass     :true,
                        responsive:{
                            0:{
                                items:3,
                            },
                            600:{
                                items:5,
                            },
                            1000:{
                                items:8,
                            }
                        }
                    });
                });
            <?php endif ?>
            <?php if ( ($style_type == 6) && ( $light_box == 6 ) ): //bootstrap-lightbox ?>
                jQuery(document).ready(function () {
                    var $grid = jQuery('.all-images').isotope({
                        itemSelector: '.single-image',
                    });
                    $grid.imagesLoaded().progress( function() {
                        $grid.isotope('layout');
                    });
                });
                jQuery(document).ready(function () {
                    // delegate calls to data-toggle="lightbox"
                    jQuery(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(event) {
                        event.preventDefault();
                        return jQuery(this).ekkoLightbox({
                            onShown: function() {
                                /* if (window.console) {
                                    return console.log('Checking our the events huh?');
                                } */
                            },
                            onNavigate: function(direction, itemIndex) {
                                if (window.console) {
                                    return console.log('Navigating '+direction+'. Current item: '+itemIndex);
                                }
                            }
                        });
                    });

                    //Programatically call
                    jQuery('#open-image').click(function (e) {
                        e.preventDefault();
                        jQuery(this).ekkoLightbox();
                    });
                    jQuery('#open-youtube').click(function (e) {
                        e.preventDefault();
                        jQuery(this).ekkoLightbox();
                    });

                    // navigateTo
                    jQuery(document).delegate('*[data-gallery="navigateTo"]', 'click', function(event) {
                        event.preventDefault();

                        var lb;
                        return jQuery(this).ekkoLightbox({
                            onShown: function() {
                                lb = this;
                                jQuery(lb.modal_content).on('click', '.modal-footer a', function(e) {
                                    e.preventDefault();
                                    lb.navigateTo(2);
                                });
                            }
                        });
                    });
                });
            <?php endif ?>
            <?php if ( ($style_type == 6) && ( $light_box == 9 ) ): //fancybox_lightbox ?>
                jQuery(document).ready(function () {
                    $('[data-fancybox="gallery-<?php echo $erw_gallery_id; ?>"]').fancybox({
                        protect: true
                    });
                });
            <?php endif ?>
        </script>

        <?php return ob_get_clean();
    }
});