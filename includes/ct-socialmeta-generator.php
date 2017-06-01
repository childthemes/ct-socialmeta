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
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {

        //add_action( 'wp_head', array( $this, 'get_socialmeta_tags' ), 1 );

    }

    /**
     * Add all social meta to frontend head tag
     *
     * @since    1.0.0
     */
    public function get_socialmeta_tags() {
        echo $this->get_socialmeta_tags_cache();
    }

    /**
     * Add all social meta to frontend head tag from cache
     *
     * @since    1.0.0
     * @return   string
     */
    public function get_socialmeta_tags_cache() {

        $trans_id  = $this->get_cache_key();
        $transient = get_transient( $trans_id );
        $lifetime  = (int) carbon_get_theme_option('ctsm_cache_lifetime');

        if ( $lifetime > 0 && $transient !== false ) {
            return $transient;
        }

        $metadata = $this->socialmeta_generator();
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
    protected function socialmeta_generator() {

        global $wp,$post;

        $metadata  = '';
        $_object   = $this->get_object();
        $def_title = $this->get('defaut_title') ? $this->get('defaut_title') : get_bloginfo('name');
        $def_desc  = $this->get('default_desc') ? $this->get('default_desc') : get_bloginfo('description');
        $imgId     = $this->get('default_image') ? $this->get('default_image') : false;

        $meta = array(
            'og:url'    => trailingslashit(home_url(add_query_arg(array(),$wp->request))),
            'og:title'  => $def_title,
            'og:description' => $def_desc,
            'og:locale' => get_locale(),
        );

        // Facebook Specific Meta Tags
        $fb_type = $this->get('facebook_type');
        if (!empty($fb_type)) {
            $meta['og:type'] = $fb_type;
        }
        if ($fb_app_id = $this->get('facebook_appid')) {
            $meta['fb:app_id'] = absint($fb_app_id);
        }
        if ($fb_admin_id = $this->get('facebook_admin')) {
            $meta['fb:admins'] = absint($fb_admin_id);
        }

        // Twitter Specific Meta Tags
        $tw_card = $this->get('twitter_style');
        if (!empty($tw_card)) {
            $meta['twitter:card'] = $tw_card;
        }
        if ($tw_username = $this->get('twitter_username')) {
            $meta['twitter:site'] = $tw_username;
        }
        if ($tw_card == 'app' && ($tw_apps = $this->get('twitter_apps','complex')) && is_array($tw_apps)) {
            foreach ($tw_apps as $tw_app) {
                if ($tw_app['_type'] == '_ctsm_twitter_app_googleplay' && !empty($tw_app['ctsm_twitter_app_googleplay_id'])) {
                    $meta['twitter:app:name:googleplay'] = $tw_app['ctsm_twitter_app_googleplay_name'];
                    $meta['twitter:app:id:googleplay'] = $tw_app['ctsm_twitter_app_googleplay_id'];
                    $meta['twitter:app:url:googleplay'] = $tw_app['ctsm_twitter_app_googleplay_url'];
                }
                elseif ($tw_app['_type'] == '_ctsm_twitter_app_iphone' && !empty($tw_app['ctsm_twitter_app_iphone_id'])) {
                    $meta['twitter:app:name:iphone'] = $tw_app['ctsm_twitter_app_iphone_name'];
                    $meta['twitter:app:id:iphone'] = $tw_app['ctsm_twitter_app_iphone_id'];
                    $meta['twitter:app:url:iphone'] = $tw_app['ctsm_twitter_app_iphone_url'];
                }
            }
        }
        elseif ($tw_card == 'summary_large_image' && ($tw_creator = $this->get('twitter_creator'))) {
            $meta['twitter:creator'] = $tw_creator;
        }

        if ($imgId) {
            $_image = ctsm_get_image_meta($imgId);
            if (!empty($_image['src'])) {
                $meta['og:image'] = $_image['src'];
                $meta['og:image:width'] = $_image['width'];
                $meta['og:image:height'] = $_image['height'];
                $meta['og:image:type'] = $_image['mime_type'];
                $meta['twitter:image'] = $_image['src'];
                $meta['twitter:image:alt'] = $_image['alt'];
            }
        }

        ksort($meta);
        foreach ($meta as $name => $content) {
            $metadata .= '<meta name="'. esc_attr($name) .'" content="'. esc_html($content) .'" />';
        }
        return $metadata;
    }

    /**
     * Get current rendered object.
     *
     * @since    1.0.0
     * @return   string
     */
    public function get_object() {
        if (!$this->object) {
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
        return $key;
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

}
