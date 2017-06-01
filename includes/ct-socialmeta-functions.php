<?php
/**
 * Custom template tags for this plugin used by theme.
 * List of static function Helper.
 *
 * functions used across both the public-facing side of the site and the admin area.
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Socialmeta
 * @subpackage CT_Socialmeta/includes
 */

/**
 * Twitter Card Styles
 *
 * @since   1.0.0
 * @return  mixed
 */
if ( ! function_exists( 'ctsm_twitter_card_styles' ) ) :
function ctsm_twitter_card_styles() {
    return apply_filters( 'ctsm_twitter_card_styles', array(
        'summary' => 'Summary Card',
        'summary_large_image' => 'Summary Card with Large Image',
        'app' => 'App Card'
    ) );
}
endif;

/**
 * Facebook Site Types
 *
 * @since   1.0.0
 * @return  mixed
 */
if ( ! function_exists( 'ctsm_facebook_site_types' ) ) :
function ctsm_facebook_site_types() {
    return apply_filters( 'ctsm_facebook_site_types', array(
        'website' => 'Website',
        'article' => 'Article',
        'profile' => 'Profile',
        'business.business' => 'Business Page',
        'product' => 'Product Page',
        'product.group' => 'Product Group',
        'product.item' => 'Product Item'
    ) );
}
endif;

/**
 * Gets the ID of the post, even if it's not inside the loop.
 *
 * @uses		 	WP_Query
 * @uses 			get_queried_object()
 * @extends 	    get_the_ID()
 * @see 			get_the_ID()
 *
 * @return 		int
 */
if ( ! function_exists( 'ctsm_get_ID' ) ) :
function ctsm_get_ID() {
	if ( is_front_page() || is_search() || is_404() || is_archive() || is_admin() ) {
		$post_id = 0;
	} elseif ( in_the_loop() ) {
        $post_id = get_the_ID();
    } else {
        /** @var $wp_query wp_query */
        global $wp_query;
        $post_id = $wp_query->get_queried_object_id();
    }
    return $post_id;
}
endif;

/**
 * Get list of registered post types
 *
 * @since   1.0.0
 * @return  mixed
 */
if ( ! function_exists( 'ctsm_post_type_options' ) ) :
function ctsm_post_type_options() {

    $options = array();

    $types = get_post_types(
        array(
            'public'   => true,
            'show_ui'  => true
        ),
        'objects',
        'and'
    );

    if (!empty($types)) {
        foreach ($types as $type) {
            $options[ $type->name ] = $type->labels->name;
        }
    }
    return $options;
}
endif;

/**
 * Get list of registered taxonomy as options
 *
 * @since   1.0.0
 * @return  mixed
 */
if ( ! function_exists( 'ctsm_taxonomy_options' ) ) :
function ctsm_taxonomy_options() {

    $options = array();

    $taxonomies = get_taxonomies(
        array(
            'public'   => true,
            'show_ui'  => true
        ),
        'objects',
        'and'
    );

    if (!empty($taxonomies)) {
        foreach ($taxonomies as $tax_name => $taxonomy) {
            $options[ $tax_name ] = $taxonomy->labels->name;
        }
    }
    return $options;
}
endif;

/**
 * Get meta data from image URL
 *
 * @since   1.0.0
 * @return  mixed
 */
if ( ! function_exists( 'ctsm_get_attachment_id_from_src' ) ) :
function ctsm_get_attachment_id_from_src($image_src) {
    global $wpdb;
    $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
    $id = $wpdb->get_var($query);
    return $id;
}
endif;

/**
 * Get meta data from image attachment ID
 *
 * @since   1.0.0
 * @return  mixed
 */
if ( ! function_exists( 'ctsm_get_image_meta' ) ) :
function ctsm_get_image_meta($attachment_id) {

    $metadata = array(
        'src' => '',
        'width' => 100,
        'height' => 100,
        'alt' => '',
        'mime_type' => ''
    );

    if (!empty($attachment_id)) {
        $image = wp_get_attachment_image_src($attachment_id, 'full', true);
        $attachment = get_post($attachment_id);
        if ($image && $attachment && $attachment->post_type == 'attachment') :
            $alt_text = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
            if(empty($alt_text)) {
                $alt_text = trim(strip_tags( $attachment->post_excerpt ));
            }
            if(empty($alt_text)) {
                $alt_text = trim(strip_tags( $attachment->post_title ));
            }
            $metadata = array(
                'src' => $image[0],
                'width' => $image[1],
                'height' => $image[2],
                'alt' => ucwords(str_replace(array('-','_'), ' ', $alt_text)),
                'mime_type' => $attachment->post_mime_type
            );
        endif;
    }
    return $metadata;
}
endif;

/**
 * Plugin Debugger
 */
function ctsm_debugger() {

    global $ct_socialmeta;

    $data = ctsm_get_attachment_id_from_src('http://dev.idjavahost.com/assets/media/2017/05/logo-big.png');

    echo "<pre style='width:100%;overflow:auto'><code>";
    print_r($data);
    echo "</code></pre>";
}
add_action( 'admin_notices', 'ctsm_debugger' );
