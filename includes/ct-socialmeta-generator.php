<?php
/**
 * Generator for meta head this plugin.
 *
 * @since      1.0.0
 * @package    CT_Socialmeta
 * @subpackage CT_Socialmeta/includes
 * @author     ChildThemes <hello@childthemes.net>
 */

class CT_Socialmeta_Generator {

    /**
     * @var string
     */
    protected $template;

    /**
     * @var WP_Post or WP_Term
     */
    protected $object;

    /**
     * @var $taxonomy
     */
    protected $taxonomies;

    /**
     * @var $post_type
     */
    protected $post_types;

    /**
     * @var mixed
     */
    public $meta = array();

    /**
     * @var $og_head
     */
    protected $og_head;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {

        add_action( 'template_redirect', array( $this, 'get_socialmeta_tags' ) );
        add_action( 'add_head_attributes', array( $this, 'socialmeta_head_attrs' ) );
        add_action( 'wp_head', array( $this, 'socialmeta_tags' ), 1 );

    }

    /**
     * Add all social meta to frontend head tag
     *
     * @since    1.0.0
     */
    public function socialmeta_tags() {
        echo $this->get_socialmeta_tags();
    }

    /**
     * Add all social meta to frontend head tag from cache
     *
     * @since    1.0.0
     * @return   string
     */
    public function get_socialmeta_tags() {

        $this->template = get_template();
        $trans_id  = $this->get_cache_key();
        $transient = get_transient( $trans_id );
        $lifetime  =  carbon_get_theme_option('ctsm_cache_lifetime');

        add_option( 'ct_socialmeta_head_support_' . $this->template, false );

        if ( $lifetime > 0 && $transient !== false ) {
            return $transient;
        }

        $metadata = '';
        $socialmeta = $this->get_socialmeta();
        foreach ($socialmeta as $name => $content) {
            $nametag = ( strpos($name, 'twitter') !== false ) ? 'name' : 'property';
            $metadata .= '<meta '. $nametag .'="'. esc_attr($name) .'" content="'. esc_attr($content) .'" />';
        }

        if ($lifetime > 0) {
            $lifetime = $lifetime > 30 ? $lifetime : MINUTE_IN_SECONDS; // reasonable timeout
            set_transient( $trans_id, $metadata, $lifetime );
        }

        return $metadata;
    }

    /**
     * Generate social meta tags.
     *
     * @since    1.0.0
     * @return   string
     */
    public function get_socialmeta() {

        if ( !empty( $this->meta ) && !empty( $this->og_head ) ) {
            return $this->meta;
        }

        global $wp,$post;

        $metadata  = '';
        $_object   = $this->get_object();
        $def_title = $this->get('defaut_title') ? $this->get('defaut_title') : get_bloginfo('name');
        $def_desc  = $this->get('default_desc') ? $this->get('default_desc') : get_bloginfo('description');
        $imgId     = $this->get('default_image') ? $this->get('default_image') : false;
        $sep_title = ($opt_sep_title = $this->get('social_title_sep')) ? ' ' . html_entity_decode($opt_sep_title) . ' ' : ' ';
        $add_title = $this->get('social_title_append') == 'yes' ? $sep_title . get_bloginfo('name') : '';

        $meta = array(
            'og:url'    => trailingslashit(home_url(add_query_arg(array(),$wp->request))),
            'og:title'  => $def_title . $add_title,
            'og:description' => $def_desc,
            'og:locale' => get_locale(),
        );

        /**
         * Facebook Meta
         */
        $fb_type = $this->get('facebook_type');

        if ($fb_app_id = $this->get('facebook_appid')) {
            $meta['fb:app_id'] = $fb_app_id;
        }
        if ($fb_admin_id = $this->get('facebook_admin')) {
            $meta['fb:admins'] = $fb_admin_id;
        }
        if ($fb_page_id = $this->get('facebook_page_id')) {
            $meta['fb:pages'] = $fb_page_id;
        }

        /**
         * Twitter Meta
         */
        $tw_card = $this->get('twitter_style');

        if ($tw_username = $this->get('twitter_username')) {
            $meta['twitter:site'] = '@'.$tw_username;
        }

        /**
         * Generate Specific Page
         */
        if ( is_author() ) {
            $fb_type = 'profile';
            $tw_card = 'summary';
            $author_id = get_the_author_meta('ID');
            $user_fb_id = carbon_get_user_meta($author_id, 'ctsm_facebook');
            $user_tw_id = carbon_get_user_meta($author_id, 'ctsm_twitter');
            $author = get_user_by( 'id', $author_id );
            $avatar = get_avatar_url( get_the_author_meta('ID'), array( 'size' => 500 ) );
            if (!empty($user_fb_id)) {
                $meta['fb:profile_id'] = $user_fb_id;
            }
            elseif (!empty($fb_admin_id)) {
                $meta['fb:profile_id'] = $fb_admin_id;
            }
            $author_name = ctsm_get_user_name( $author_id );
            $meta['og:title'] = $author_name . $add_title;
            $meta['og:description'] = sprintf( __('All post created by %s', 'ct-socialmeta'), $author_name );
            if (!empty($avatar)) {
                $imgId = false;
                $meta['og:image'] = $avatar;
                $meta['og:image:width'] = 500;
                $meta['og:image:height'] = 500;
                $meta['twitter:image'] = $avatar;
                $meta['twitter:image:alt'] = sprintf( __('Profile picture of %s', 'ct-socialmeta'), $author_name );
            }
            if (!empty($user_tw_id)) {
                $meta['twitter:creator'] = '@'.$user_tw_id;
            }
        }
        elseif ( is_archive() && is_date() ) {
            $fb_type = 'article';
            $tw_card = 'summary';
            $meta['og:title'] = esc_attr__( 'Archive by Date', 'ct-socialmeta' ) . $add_title;
            $date_desc = esc_attr__( 'List of article posted at', 'ct-socialmeta' );
            $date_string = '';
            if ( $date_day = get_query_var('day') ) {
                $date_string .= ' '.$date_day;
            }
            if ( $date_month = get_query_var('monthnum') ) {
                $date_string .= ' '.date('F', mktime(0, 0, 0, $date_month, 10));
            }
            if ( $date_year = get_query_var('year') ) {
                $date_string .= ' '.$date_year;
            }
            $meta['og:description'] = $date_desc . $date_string;
            $meta['og:updated_time'] = date('c', strtotime( $date_string ));
            $meta['article:modified_time'] = $meta['og:updated_time'];
            $meta['article:section'] = __('Archive', 'ct-socialmeta');
        }

        $use_address = true;
        $use_article = true;

        /**
         * Get custom value by object type.
         */
        if ( $_object && ! is_author() && ! is_front_page() && ! is_home() ) {
            if ( $_object instanceof WP_Post ) {
                $fb_type = 'article';
                $tw_card = 'summary';

                $meta['og:url'] = get_permalink( $_object->ID );
                $meta['og:title'] = $_object->post_title . $add_title;
                $meta['og:updated_time'] = date('c', strtotime( $_object->post_modified ));
                $meta['og:description'] = wp_strip_all_tags( get_the_excerpt(), true );
                if (empty($meta['og:description'])) {
                    $content = apply_filters( 'the_content', $_object->post_content );
                    $meta['og:description'] = wp_trim_words( wp_strip_all_tags($content, true), 30, '.' );
                }

                $user_fb_id = carbon_get_user_meta($_object->post_author, 'ctsm_facebook');
                $user_tw_id = carbon_get_user_meta($_object->post_author, 'ctsm_twitter');
                if (!empty($user_fb_id)) {
                    $meta['fb:profile_id'] = $user_fb_id;
                }
                if (!empty($user_tw_id)) {
                    $meta['twitter:creator'] = '@'.esc_attr($user_tw_id);
                }

                if ( has_post_thumbnail( $_object->ID ) ) {
                    $tw_card = 'summary_large_image';
                    $imgId =  get_post_meta( $_object->ID, '_thumbnail_id', true );
                }
                elseif ( $first_obj_img = ctsm_get_first_image( $_object->ID ) ) {
                    $tw_card = 'summary_large_image';
                    if ($try_get_image_id = ctsm_get_attachment_id_from_src($first_obj_img)) {
                        $imgId = $try_get_image_id;
                    } else {
                        $imgId = false;
                        $uploads = wp_upload_dir();
                        $image_path = str_replace( $uploads['baseurl'], $uploads['basedir'], $first_obj_img );
                        $img_size = getimagesize($image_path);
                        if ($img_size && is_array($img_size)) {
                            $meta['og:image:width'] = $img_size[0];
                            $meta['og:image:height'] = $img_size[1];
                        }
                        $meta['og:image'] = esc_url($first_obj_img);
                        $meta['og:image:alt'] = $_object->post_title;
                        $meta['twitter:image'] = esc_url($first_obj_img);
                        $meta['twitter:image:alt'] = $_object->post_title;
                    }
                }

                // Custom override is enabled
                if ( $this->is_post_type_active( $_object->post_type ) ) {
                    if ( $meta_title = $this->meta('defaut_title') ) {
                        $meta['og:title'] = $meta_title;
                    }
                    if ( $meta_desc = $this->meta('default_desc') ) {
                        $meta['og:description'] = $meta_desc;
                    }
                    if ( $meta_image = $this->meta('default_image') ) {
                        $imgId = absint($meta_image);
                    }
                    // Override Facebook
                    if ( $this->meta('facebook_is_default') !== 'yes' ) {
                        $meta_fb_type = $this->meta('facebook_type');
                        if ( !empty( $meta_fb_type ) ) {
                            $fb_type = $meta_fb_type;
                        }
                        if ( $meta_fb_type == 'profile' && ( $meta_fb_profile = $this->meta('facebook_profile') ) ) {
                            $meta['fb:profile_id'] = $meta_fb_profile;
                        }
                        elseif ( $meta_fb_type == 'article' ) {
                            $use_article = false;
                            if ( $meta_fb_article_author = $this->meta('facebook_article_author') ) {
                                $meta['article:author'] = $meta_fb_article_author;
                            }
                            if ( $meta_fb_article_section = $this->meta('facebook_article_section') ) {
                                $meta['article:section'] = $meta_fb_article_section;
                            }
                        }
                        elseif ( $meta_fb_type == 'business.business' ) {
                            $use_address = false;
                            $meta_fb_address = array( 'street_address', 'locality', 'region', 'postal_code', 'country_name', 'phone_number', 'website' );
                            foreach ($meta_fb_address as $meta_fb_addr) {
                                $meta_fb_addr_data = $this->meta('facebook_business_'.$meta_fb_addr);
                                $meta_fb_addr_meta = 'business:contact_data:'.$meta_fb_addr;
                                if ( !empty($meta_fb_addr_data) ) {
                                    $meta[ $meta_fb_addr_meta ] = $meta_fb_addr_data;
                                }
                            }
                        }
                        elseif ( $meta_fb_type == 'product' ) {
                            $meta_fb_products = array( 'upc', 'availability', 'brand', 'category', 'price_amount', 'price_currency' );
                            foreach ($meta_fb_products as $meta_fb_prod) {
                                $meta_fb_prod_data = $this->meta('facebook_product_'.$meta_fb_prod);
                                $meta_fb_prod_meta = 'product:'.str_replace('_',':',$meta_fb_prod);
                                if ( !empty($meta_fb_prod_data) ) {
                                    $meta[ $meta_fb_prod_meta ] = $meta_fb_prod_data;
                                }
                            }
                        }
                    }
                    // Override Twitter
                    if ( $this->meta('twitter_is_default') !== 'yes' ) {
                        $meta_tw_card = $this->meta('facebook_type');
                        if ( !empty( $meta_tw_card ) ) {
                            $tw_card = $meta_tw_card;
                        }
                        if ( $meta_tw_card == 'summary_large_image' && ( $meta_tw_creator = $this->meta('twitter_creator') ) ) {
                            $meta['twitter:creator'] = '@'.esc_attr( $meta_tw_creator );
                        }
                    }
                }

                //Specific facebook
                if ($fb_type == 'article' && $use_article) {
                    if (!isset($meta['article:author'])) {
                        $meta['article:author'] = $this->get('facebook_article_author') ?
                             $this->get('facebook_article_author')
                            : ctsm_get_user_name( $_object->post_author );
                    }
                    if ( ($article_publisher = $this->get('facebook_page_id')) && !isset($meta['article:publisher'])) {
                        $meta['article:publisher'] = $article_publisher;
                    }

                    $meta['article:modified_time'] = date('c', strtotime( $_object->post_modified ));
                    $meta['article:published_time'] = date('c', strtotime( $_object->post_date ));

                    if (!isset($meta['article:section'])) {
                        $categories = get_the_category( $_object->ID );
                        if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                            $meta['article:section'] = $categories[0]->name;
                        } elseif ($article_section = $this->get('facebook_article_section')) {
                            $meta['article:section'] = $article_section;
                        }
                    }
                    if ( ! empty( $user_fb_id ) && !isset($meta['article:author']) ) {
                        $meta['article:author'] = $user_fb_id;
                    }
                }
            }
        }

        /**
         * Generate Image
         */
        if ($imgId) {
            $_image = ctsm_get_image_meta($imgId);
            if (!empty($_image['src'])) {
                $meta['og:image'] = $_image['src'];
                $meta['og:image:width'] = $_image['width'];
                $meta['og:image:height'] = $_image['height'];
                $meta['og:image:type'] = $_image['mime_type'];
                $meta['og:image:alt'] = $_image['alt'];
                $meta['twitter:image'] = $_image['src'];
                $meta['twitter:image:alt'] = $_image['alt'];
            }
        }

        /**
         * Facebook Specific Meta Tags
         */
        if (!empty($fb_type)) {
            $this->og_head = $fb_type;
            $meta['og:type'] = $fb_type;
        }
        if ( in_array( $fb_type, array( 'place', 'business.business' ) ) ) {
            if (($fb_place_lat = $this->get('facebook_place_lat')) && !isset($meta['place:location:latitude'])) {
                $meta['place:location:latitude'] = floatval($fb_place_lat);
            }
            if (($fb_place_lng = $this->get('facebook_place_long')) && !isset($meta['place:location:longitude'])) {
                $meta['place:location:longitude'] = floatval($fb_place_lng);
            }
        }
        if ($fb_type == 'profile' && ($fb_profile_id = $this->get('facebook_profile')) && !isset($meta['fb:profile_id'])) {
            $meta['fb:profile_id'] = $fb_profile_id;
        }
        if ( $fb_type == 'business.business' && $use_address ) {
            $fb_address = array( 'street_address', 'locality', 'region', 'postal_code', 'country_name', 'phone_number', 'website' );
            foreach ($fb_address as $fb_addr) {
                $fb_addr_data = $this->get('facebook_business_'.$fb_addr);
                $fb_addr_meta = 'business:contact_data:'.$fb_addr;
                if ( !empty($fb_addr_data) && !isset( $meta[ $fb_addr_meta ] ) ) {
                    $meta[ $fb_addr_meta ] = $fb_addr_data;
                }
            }
        }

        /**
         * Twitter Specific Meta Tags
         */
        if (!empty($tw_card)) {
            $meta['twitter:card'] = $tw_card;
        }
        elseif (!isset($meta['twitter:creator']) && $tw_card == 'summary_large_image' && ($tw_creator = $this->get('twitter_creator'))) {
            $meta['twitter:creator'] = '@'.$tw_creator;
        }

        if ( ! empty( $meta['og:title'] ) ) {
            $meta['twitter:title'] = $meta['og:title'];
        }

        if ( ! empty( $meta['og:description'] ) ) {
            $meta['twitter:description'] = $meta['og:description'];
        }

        /**
         * Applications Properties
         */
        $apps = array( 'iphone', 'ipad', 'android', 'ios', 'win' );
        foreach ($apps as  $app) {
            $app_name = $this->get( 'app_name_' . $app );
            $app_id   = $this->get( 'app_id_' . $app );
            $app_url  = $this->get( 'app_url_' . $app );
            if ( empty( $app_name ) || empty( $app_id ) || empty( $app_url ) ) {
                continue;
            }
            if ( ! empty( $fb_app_id ) ) {
                if ( $app == 'win' && ( $app_win_type = $this->get('app_uses_win') ) ) {
                    $app_type = ( $app_win_type == 'desktop' ) ? 'windows' : ( $app_win_type == 'desktop_mobile' ? 'windows_phone' : 'windows_universal' );
                } else {
                    $app_type = $app;
                }
                $app_meta_url  = 'al:'.$app_type.':url';
                $app_meta_name = 'al:'.$app_type.':app_name';
                $app_meta_id   = 'al:'.$app_type.':';
                $app_meta_id  .= ($app == 'android') ? 'package' : ( $app == 'win' ? 'app_id' : 'app_store_id' );
                $meta[ $app_meta_url ] = esc_url( $app_url );
                $meta[ $app_meta_id ] = esc_attr( $app_id );
                $meta[ $app_meta_name ] = esc_attr( $app_name );
            }
            if ( $tw_card == 'app' && ! in_array( $app, array( 'ios', 'win' ) ) ) {
                $app_tw_name = ( $app == 'android' ) ? 'googleplay' : $app;
                $meta[ 'twitter:app:name:'.$app_tw_name ] = esc_attr( $app_name );
                $meta[ 'twitter:app:id:'.$app_tw_name ] = esc_attr( $app_id );
                $meta[ 'twitter:app:url:'.$app_tw_name ] = esc_url( $app_url );
            }
        }

        /**
         * Hook for theme or plugin
         */
        $this->meta = apply_filters( 'ct_socialmeta_tags', $meta );

        /**
         * Finalize
         */
        ksort($this->meta);
        return $this->meta;
    }

    /**
     * Get current rendered object.
     *
     * @since    1.0.0
     * @return   string
     */
    public function get_object() {
        if ($this->object === null) {
            $_object = get_queried_object();
            if ( ! empty( $_object ) ) {
                $this->object = $_object;
            } else {
                $this->object = false;
            }
        }
        return $this->object;
    }

    /**
     * Get generated cache key
     *
     * @since    1.0.0
     * @return   string
     */
    public function get_cache_key() {

        $key = 'ct_socialmeta_head';
        $_object = $this->get_object();

        if ( ( $_object instanceof WP_Post ) && $this->is_post_type_active( $_object->post_type ) ) {
            $key .= '_'. $_object->post_type .'_' . $_object->ID;
        }
        elseif ( ( $_object instanceof WP_Term ) && $this->is_taxonomy_active( $_object->taxonomy ) ) {
            $key .= '_'. $_object->taxonomy .'_'. $_object->term_id;
        }
        elseif ( ( $_object instanceof WP_User ) && !empty( $_object->ID ) ) {
            $key .= '_user_' . $_object->ID;
        }
        elseif ( is_archive() ) {
            $key .= '_archive';
            if ( $year = get_query_var('year') ) {
                $key .= "_$year";
            }
            if ( $month = get_query_var('monthnum') ) {
                $key .= "_$month";
            }
            if ( $day = get_query_var('day') ) {
                $key .= "_$day";
            }
        }
        return $key;
    }

    /**
     * Generate custom head attribute for Open Graph
     *
     * @since    1.0.0
     * @return   void
     */
    public function socialmeta_head_attrs() {

        update_option( 'ct_socialmeta_head_support_' . get_template(), true );

        if ( empty( $this->og_head ) ) {
            return;
        }

        switch ( $this->og_head ) {
            case 'business.business':
                $tag = 'prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# business: http://ogp.me/ns/business#"'; break;
            case 'product':
                $tag = 'prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# product: http://ogp.me/ns/product#"'; break;
            case 'place':
                $tag = 'prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# place: http://ogp.me/ns/place#"'; break;
            case 'profile':
                $tag = 'prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# profile: http://ogp.me/ns/profile#"'; break;
            case 'article':
                $tag = 'prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#"'; break;
            default:
                $tag = '';
        }
        echo !empty($tag) ? ' '.$tag : '';
    }

    /**
     * Get enabled post type for custom individual social meta
     *
     * @since    1.0.0
     * @return   mixed
     */
    public function is_post_type_active( $post_type ) {
        if (empty($this->post_types)) {
            $this->post_types = (array) $this->get('metabox_post_type');
        }
        return ( in_array( $post_type, $this->post_types ) );
    }

    /**
     * Get enabled taxonomy for custom individual social meta
     *
     * @since    1.0.0
     * @return   mixed
     */
    public function is_taxonomy_active( $taxonomy ) {
        if (empty($this->taxonomies)) {
            $this->taxonomies = (array) $this->get('metabox_taxonomy');
        }
        return ( in_array( $taxonomy, $this->taxonomies ) );
    }

    /**
     * Just shorthand to get setting value
     *
     * @since    1.0.0
     * @param    $field   field name
     * @param    $type    field type
     * @return   mixed | string | int
     */
    public function get( $field, $type = null ) {
        $field = (strpos($field, 'ctsm_') !== false) ? $field : 'ctsm_'.$field;
        return carbon_get_theme_option( $field, $type );
    }

    /**
     * Just shorthand to get post meta value
     *
     * @since    1.0.0
     * @param    $field   field name
     * @param    $type    field type
     * @return   mixed | string | int
     */
    public function meta( $field, $type = null ) {
        if (!$this->object) {
            return;
        }
        $field = (strpos($field, 'ctsm_') !== false) ? $field : 'ctsm_'.$field;
        return carbon_get_post_meta( $this->object->ID, $field, $type );
    }

}
