<?php
/*
Plugin Name: Advanced Custom Fields: Image Crop Add-on
Plugin URI: https://github.com/andersthorborg/ACF-Image-Crop
Description: An image field making it possible/required for the user to crop the selected image to the specified image size or dimensions
Version: 1.4.7
Author: Anders Thorborg
Author URI: http://thorb.org
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf-image_crop', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );




// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_image_crop( $version ) {
    include_once('acf-image-crop-v5.php');

}

add_action('acf/include_field_types', 'include_field_types_image_crop');




// 3. Include field type for ACF4
function register_fields_image_crop() {

    include_once('acf-image-crop-v4.php');

}

add_action('acf/register_fields', 'register_fields_image_crop');


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'acf_image_crop_action_links' );

function acf_image_crop_action_links( $links ) {
// changed
   $links[] = '<a href="'. get_admin_url(null, 'options-media.php') .'">'.__('Settings','acf-image_crop').'</a>';
// changed END
   return $links;
}

?>
