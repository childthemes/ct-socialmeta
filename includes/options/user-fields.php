<?php
/**
 * User meta data social profile.
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Socialmeta
 * @subpackage CT_Socialmeta/includes
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('user_meta', __('Social Profile', 'ct-socialmeta'))
->add_fields(array(
    Field::make('text', 'ctsm_facebook', __('Facebook Profile ID', 'ct-socialmeta'))
    ->help_text(sprintf(__('get your profile ID at %s', 'ct-socialmeta'), '<a href="https://findmyfbid.com/" target="_blank">findmyfbid.com</a>'))
    ->add_class('regular-text'),
    Field::make('text', 'ctsm_twitter', __('Twitter Username', 'ct-socialmeta'))
    ->help_text(__('Your profile username without @', 'ct-socialmeta'))
    ->add_class('regular-text')
));
