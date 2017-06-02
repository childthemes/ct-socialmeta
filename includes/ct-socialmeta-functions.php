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
        'place' => 'Place',
        'business.business' => 'Business Page',
        'product' => 'Product Page',
        //'product.group' => 'Product Group',
        //'product.item' => 'Product Item'
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
 * Get first image ID from post object ID.
 *
 * @since   1.0.0
 * @param   $post_id  int
 * @return  int
 */
if ( ! function_exists( 'ctsm_get_first_image' ) ) :
function ctsm_get_first_image($post_id) {
    $images = get_posts(
        array(
            'post_type'      => 'attachment',
            'post_parent'    => $post_id,
            'posts_per_page' => 1, /* Save memory, only need one */
        )
    );
    $image = !empty($images) ? $images[0] : false;
    if ($image) {
        return wp_get_attachment_image_src($image, 'full', true)[0];
    }
    $post = get_post($post_id);
    if (!empty($post->post_content)) {
        $content = apply_filters( 'the_content', $post->post_content );
        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
        if (!empty($matches[1][0])) {
            $image = $matches[1][0];
        }
    }
    return $image;
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
 * Hook after post meta saved.
 *
 * @param $post_id  int
 */
if ( ! function_exists('ctsm_after_save_meta') ) :
function ctsm_after_save_meta($post_id) {
    $cache_key = 'ct_socialmeta_head_' . get_post_type($post_id) . '_' . $post_id;
    delete_transient( $cache_key );
}
add_action( 'carbon_after_save_post_meta', 'ctsm_after_save_meta' );
endif;

/**
 * Get user friendly name
 *
 * @param $user_id  int
 */
if ( ! function_exists('ctsm_get_user_name') ) :
function ctsm_get_user_name($user_id) {
    $myname = '';
    if ($display_name = get_user_meta($user_id, 'display_name', true)) {
        $myname = $display_name;
    }
    if ($nick_name = get_user_meta($user_id, 'nickname', true)) {
        $myname = $nick_name;
    }
    elseif ($first_name = get_user_meta($user_id, 'first_name', true)) {
        $myname = $first_name;
        if ($last_name = get_user_meta($user_id, 'last_name', true)) {
            $myname .= ' ' . $last_name;
        }
    } else {
        $myname = get_user_meta($user_id, 'username', true);
    }
    return $myname;
}
endif;

/**
 * Plugin Debugger
 */
function ctsm_admin_debugger() {

    global $ct_socialmeta;

    $data = ctsm_get_attachment_id_from_src('http://dev.idjavahost.com/assets/media/2017/05/logo-big.png');

    echo "<pre style='width:100%;overflow:auto'><code>";
    print_r($data);
    echo "</code></pre>";
}
//add_action( 'admin_notices', 'ctsm_admin_debugger' );

/**
 * Plugin Debugger
 */
function ctsm_front_debugger() {

    global $ct_socialmeta;

    $data = $ct_socialmeta->generator->meta;
    $object = ctsm_get_user_name(1);

    echo "<br /><br /><pre style='width:100%;overflow:auto'><code>";
    var_dump( carbon_get_theme_option('ctsm_facebook_page_id') );
    echo "\n";
    var_dump( $data );
    echo "</code></pre>";
}
add_action( 'wp_head', 'ctsm_front_debugger', 9999 );
