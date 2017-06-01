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
    Field::make('select', 'ctsm_facebook_type', __('Facebook Site Type', 'ct-socialmeta'))->set_options('ctsm_facebook_site_types'),
    Field::make('text', 'ctsm_facebook_page', __('Facebook Page URL', 'ct-socialmeta')),
    Field::make('text', 'ctsm_facebook_admin', __('Facebook Admin ID', 'ct-socialmeta'))->set_width(50)
        ->help_text(__('Facebook page or app admin profile ID', 'ct-socialmeta')),
    Field::make('text', 'ctsm_facebook_appid', __('Facebook App ID', 'ct-socialmeta'))->set_width(50)
        ->help_text(__('Facebook developer App ID', 'ct-socialmeta')),
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
        )),
    Field::make('complex', 'ctsm_twitter_apps', __('Mobile App Details', 'ct-socialmeta'))
        ->add_fields('ctsm_twitter_app_iphone', __('iPhone App', 'ct-socialmeta'), array(
            Field::make('text', 'ctsm_twitter_app_iphone_name', __('Name of your iPhone App', 'ct-socialmeta'))->set_width('33.333333'),
            Field::make('text', 'ctsm_twitter_app_iphone_id', __('App ID (.i.e. "307234931")', 'ct-socialmeta'))->set_width('33.333333'),
            Field::make('text', 'ctsm_twitter_app_iphone_url', __('App’s custom URL scheme', 'ct-socialmeta'))->set_width('33.333333'),
        ))
        ->set_header_template('iPhone App<# if (ctsm_twitter_app_iphone_name) { #> : {{ ctsm_twitter_app_iphone_name }}<# } #>')
        ->add_fields('ctsm_twitter_app_googleplay', __('Android App', 'ct-socialmeta'), array(
            Field::make('text', 'ctsm_twitter_app_googleplay_name', __('Name of your Android App', 'ct-socialmeta'))->set_width('33.333333'),
            Field::make('text', 'ctsm_twitter_app_googleplay_id', __('App ID (.i.e. "com.android.app")', 'ct-socialmeta'))->set_width('33.333333'),
            Field::make('text', 'ctsm_twitter_app_googleplay_url', __('App’s custom URL scheme', 'ct-socialmeta'))->set_width('33.333333'),
        ))
        ->set_header_template('Android App<# if (ctsm_twitter_app_googleplay_name) { #> : {{ ctsm_twitter_app_googleplay_name }}<# } #>')
        ->set_layout('tabbed-horizontal')
        ->set_max(4)
        ->setup_labels(array(
            'plural_name' => __('Apps', 'ct-socialmeta'),
            'singular_name' => __('App', 'ct-socialmeta')
        ))
        ->set_conditional_logic(array(
            array(
                'field'   => 'ctsm_twitter_style',
                'value'   => 'app'
            )
        )),
))
/**
 * GOOGLE+ SETTINGS
 */
->add_tab( __('Google+ Settings', 'ct-socialmeta'), array(
    Field::make('text', 'ctsm_gplus_link', __('Google+ Page URL', 'ct-socialmeta')),
))

;
