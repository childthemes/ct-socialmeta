<?php
/**
 * Plugin Options.
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Socialmeta
 * @subpackage CT_Socialmeta/includes
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

$fb_site_type = array();
foreach (ctsm_facebook_site_types() as $key => $fb_type) {
    if ( strpos($key, 'product') === false ) {
        $fb_site_type[ $key ] = $fb_type;
    }
}

Container::make('theme_options', __('Child Social Meta', 'ct-socialmeta'))
->set_page_parent('options-general.php')
/**
 * GENERAL SETTINGS
 */
->add_tab( __('General', 'ct-socialmeta'), array(
    Field::make('separator', 'ctsm_social_values', __('Default Social Meta', 'ct-socialmeta')),
    Field::make('text', 'ctsm_defaut_title', __('Default Title', 'ct-socialmeta'))->set_required(true)->set_default_value(get_bloginfo('name')),
    Field::make('textarea', 'ctsm_default_desc', __('Default Description', 'ct-socialmeta'))->set_required(true)->set_rows(2)->set_default_value(get_bloginfo('description')),
    Field::make('image', 'ctsm_default_image', __('Default Image', 'ct-socialmeta'))->set_required(true)->set_width(30),
    Field::make('text', 'ctsm_default_image_width', __('Default Image Width (px)', 'ct-socialmeta'))->set_width(35),
    Field::make('text', 'ctsm_default_image_height', __('Default Image Height (px)', 'ct-socialmeta'))->set_width(35),
    Field::make('separator', 'ctsm_social_settings', __('Social Meta Settings', 'ct-socialmeta')),
    Field::make('checkbox', 'ctsm_social_title_append', __('Append current title with site title', 'ct-socialmeta'))
        ->set_option_value('yes')->set_default_value('yes')->set_width(70),
    Field::make('text', 'ctsm_social_title_sep', __('Title Separator', 'ct-socialmeta'))->set_default_value('-')->set_width(30),
    Field::make('set', 'ctsm_metabox_post_type', sprintf(__('Allow custom individual social meta on all these %s', 'ct-socialmeta'), __('post type','ct-socialmeta')))
        ->add_options('ctsm_post_type_options')
        ->set_default_value(array('post','page')),
    Field::make('set', 'ctsm_metabox_taxonomy', sprintf(__('Allow custom individual social meta on all these %s', 'ct-socialmeta'), __('taxonomy','ct-socialmeta')))
        ->add_options('ctsm_taxonomy_options')
        ->set_default_value(array('category')),
    Field::make('text', 'ctsm_cache_lifetime', __('Meta Tag Cache Lifetime', 'ct-socialmeta'))->set_required(true)
        ->help_text(__('How many seconds the meta tag is being cached. e.g. 3600 for 1 hour, fill 0 to disable cache.'))
        ->set_default_value('43200')
))
/**
 * FACEBOOK SETTINGS
 */
->add_tab( __('Facebook Settings', 'ct-socialmeta'), array(
    Field::make('select', 'ctsm_facebook_type', __('Facebook Site Type', 'ct-socialmeta'))->set_options( $fb_site_type ),
    Field::make('text', 'ctsm_facebook_page_id', __('Facebook Page ID', 'ct-socialmeta'))->set_width(50)
        ->help_text(sprintf(__('Find your ID at %s', 'ct-socialmeta'), '<a href="https://findmyfbid.com/" target="_blank">FindMyFbId</a>')),
    Field::make('text', 'ctsm_facebook_page', __('Facebook Page URL', 'ct-socialmeta'))->set_width(50)
        ->help_text(__('e.g. http://www.facebook.com/YourFacebookPage', 'ct-socialmeta')),
    Field::make('text', 'ctsm_facebook_admin', __('Facebook Admin ID', 'ct-socialmeta'))->set_width(50)
        ->help_text(__('Facebook page or app admin profile ID', 'ct-socialmeta')),
    Field::make('text', 'ctsm_facebook_appid', __('Facebook App ID', 'ct-socialmeta'))->set_width(50)
        ->help_text(__('Facebook developer App ID', 'ct-socialmeta')),

    // ARTICLE
    Field::make('text', 'ctsm_facebook_article_author', __('Default Author Profile ID', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'article' ) )),
    Field::make('text', 'ctsm_facebook_article_section', __('Default Article Section/Category', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'article' ) )),

    // PROFILE
    Field::make('text', 'ctsm_facebook_profile', __('Default Facebook Profile ID', 'ct-socialmeta'))
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'profile' ) )),

    // BUSINESS
    Field::make('separator', 'ctsm_facebook_business_sep', __('Business Address', 'ct-socialmeta'))
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' ) )),
    Field::make('text', 'ctsm_facebook_business_street_address', __('Street Address', 'ct-socialmeta'))
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' ) )),
    Field::make('text', 'ctsm_facebook_business_locality', __('City', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' ) )),
    Field::make('text', 'ctsm_facebook_business_region', __('Region', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' ) )),
    Field::make('text', 'ctsm_facebook_business_postal_code', __('Post Code', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' ) )),
    Field::make('text', 'ctsm_facebook_business_country_name', __('Country', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' ) )),
    Field::make('text', 'ctsm_facebook_business_phone_number', __('Phone', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' ) )),
    Field::make('text', 'ctsm_facebook_business_website', __('Website', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' ) )),

    // BUSINESS OR PLACE
    Field::make('separator', 'ctsm_facebook_place_desc', __('Place Coordinates', 'ct-socialmeta') . '<br /><small style="display: block;font-size: 12px;font-weight: 400;line-height: 2;">'. sprintf(__('Find your Latitute and Longitude at %s.','ct-socialmeta'), '<a href="http://www.latlong.net/" target="_blank">latlong.net</a>') .'</small>')
        ->set_conditional_logic(array(
            array( 'field' => 'ctsm_facebook_type', 'compare' => 'IN', 'value' => array( 'place', 'business.business' ) )
        )),
    Field::make('text', 'ctsm_facebook_place_lat', __('Location Latitude', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array(
            array( 'field' => 'ctsm_facebook_type', 'compare' => 'IN', 'value' => array( 'place', 'business.business' ) )
        )),
    Field::make('text', 'ctsm_facebook_place_long', __('Location Longitude', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array(
            array( 'field' => 'ctsm_facebook_type', 'compare' => 'IN', 'value' => array( 'place', 'business.business' ) )
        )),
))
/**
 * TWITTER SETTINGS
 */
->add_tab( __('Twitter Settings', 'ct-socialmeta'), array(
    Field::make('select', 'ctsm_twitter_style', __('Twitter Card', 'ct-socialmeta'))
        ->help_text(__('specify the type of card for your content', 'ct-socialmeta'))
        ->set_options('ctsm_twitter_card_styles'),
    Field::make('text', 'ctsm_twitter_username', __('Twitter Username', 'ct-socialmeta'))
        ->help_text(__('Twitter username without @', 'ct-socialmeta')),
    Field::make('text', 'ctsm_twitter_creator', __('Twitter Creator', 'ct-socialmeta'))
        ->help_text(__('Twitter username without @, Used with Summary Card with Large Image'))
        ->set_conditional_logic(array(
            array(
                'field'   => 'ctsm_twitter_style',
                'value'   => 'summary_large_image'
            )
        ))
))
/**
 * GOOGLE+ SETTINGS
 */
->add_tab( __('Google Settings', 'ct-socialmeta'), array(
    Field::make('text', 'ctsm_gplus_link', __('Google+ Page URL', 'ct-socialmeta')),
))
/**
 * MOBILE APPS
 */
->add_tab( __('Application Properties', 'ct-socialmeta'), array(
    Field::make('separator', 'ctsm_apps_desc', '<small style="font-size:13px;font-weight:400;">'. __('Applcation details is required for Twitter Card Apps and useful for Facebook mobile sharing and stories (Required facebook App ID and Page ID).', 'ct-socialmeta').'</small>'),

    Field::make('separator', 'ctsm_app_iphone_sep', __('iPhone App', 'ct-socialmeta')),
    Field::make('text', 'ctsm_app_name_iphone', __('App Name', 'ct-socialmeta'))->set_width('33.333333'),
    Field::make('text', 'ctsm_app_id_iphone', __('App Store ID (.i.e. "307234931")', 'ct-socialmeta'))->set_width('33.333333'),
    Field::make('text', 'ctsm_app_url_iphone', __('App’s URL', 'ct-socialmeta'))->set_width('33.333333'),

    Field::make('separator', 'ctsm_app_ipad_sep', __('iPad App', 'ct-socialmeta')),
    Field::make('text', 'ctsm_app_name_ipad', __('App Name', 'ct-socialmeta'))->set_width('33.333333'),
    Field::make('text', 'ctsm_app_id_ipad', __('App Store ID (.i.e. "307234931")', 'ct-socialmeta'))->set_width('33.333333'),
    Field::make('text', 'ctsm_app_url_ipad', __('App’s URL', 'ct-socialmeta'))->set_width('33.333333'),

    Field::make('separator', 'ctsm_app_android_sep', __('Android App', 'ct-socialmeta')),
    Field::make('text', 'ctsm_app_name_android', __('App Name', 'ct-socialmeta'))->set_width('33.333333'),
    Field::make('text', 'ctsm_app_id_android', __('Google Play ID (.i.e. "com.android.app")', 'ct-socialmeta'))->set_width('33.333333'),
    Field::make('text', 'ctsm_app_url_android', __('App’s URL', 'ct-socialmeta'))->set_width('33.333333'),

    Field::make('separator', 'ctsm_app_ios_sep', __('iOS App', 'ct-socialmeta')),
    Field::make('text', 'ctsm_app_name_ios', __('App Name', 'ct-socialmeta'))->set_width('33.333333'),
    Field::make('text', 'ctsm_app_id_ios', __('App Store ID (.i.e. "307234931")', 'ct-socialmeta'))->set_width('33.333333'),
    Field::make('text', 'ctsm_app_url_ios', __('App’s URL', 'ct-socialmeta'))->set_width('33.333333'),

    Field::make('separator', 'ctsm_app_win_sep', __('Windows App', 'ct-socialmeta')),
    Field::make('text', 'ctsm_app_name_win', __('App Name', 'ct-socialmeta'))->set_width(25),
    Field::make('text', 'ctsm_app_id_win', __('Package Family Name', 'ct-socialmeta'))->set_width(25),
    Field::make('text', 'ctsm_app_url_win', __('App’s URL', 'ct-socialmeta'))->set_width(25),
    Field::make('select', 'ctsm_app_uses_win', __('Windows Platform', 'ct-socialmeta'))->set_width(25)
    ->set_options(array(
        'desktop' => __('Desktop Only', 'ct-socialmeta'),
        'desktop_mobile' => __('Desktop and Mobile', 'ct-socialmeta'),
        'universal' => __('Universal', 'ct-socialmeta'),
    ))
))

;
