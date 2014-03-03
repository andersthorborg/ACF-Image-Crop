<?php
/*
Plugin Name: Advanced Custom Fields: Image Crop Add-on
Plugin URI: https://github.com/andersthorborg/ACF-Image-Crop
Description: An image field making it possible/required for the user to crop the selected image to the specified image size or dimensions
Version: 0.8
Author: Anders Thorborg
Author URI: http://thorb.org
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


class acf_field_image_crop_plugin
{
	/*
	*  Construct
	*
	*  @description:
	*  @since: 3.6
	*  @created: 1/04/13
	*/

	function __construct()
	{
		// set text domain
		/*
		$domain = 'acf-image_crop';
		$mofile = trailingslashit(dirname(__File__)) . 'lang/' . $domain . '-' . get_locale() . '.mo';
		load_textdomain( $domain, $mofile );
		*/


		// version 4+
		add_action('acf/register_fields', array($this, 'register_fields'));


		// version 3-
		add_action('init', array( $this, 'init' ), 5);
	}


	/*
	*  Init
	*
	*  @description:
	*  @since: 3.6
	*  @created: 1/04/13
	*/

	function init()
	{
		if(function_exists('register_field'))
		{
			register_field('acf_field_image_crop', dirname(__File__) . '/image_crop-v3.php');
		}
	}

	/*
	*  register_fields
	*
	*  @description:
	*  @since: 3.6
	*  @created: 1/04/13
	*/

	function register_fields()
	{
		include_once('image_crop-v4.php');
	}

}

new acf_field_image_crop_plugin();

?>
