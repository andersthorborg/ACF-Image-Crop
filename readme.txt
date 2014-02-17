=== Advanced Custom Fields: Image Crop Add-on ===
Contributors: andersthorborg
Tags: afc, advanced custom fields, image crop, image, crop
Requires at least: 3.5
Tested up to: 3.8.1
Stable tag: 0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An image field making it possible/required for the user to crop the selected image to the specified image size or dimensions

== Description ==

ACF image crop is an extended version of the native Image-field in ACF.
The field gives the developer/administrator the option to predefine a size for the image, which the user is prompted to crop on the various edit screens. This solves the common issue of images being cropped inappropriately by the automated center-crop, that wordpress performs.

The plugin supports the defined image sizes as well as a custom option, enabling the developer to specify the dimensions from within the field edit screen.

The field can be configured to enforce a hard crop or a minimal-dimension-based crop. The hard crop will lock the aspect ratio of the crop where as the minimal-dimension-based crop will not allow the user to crop the image below the specified dimensions. 

This plugin diverts from plugins like [Manual Image Crop](http://wordpress.org/plugins/manual-image-crop/) in that when the user crops an image, a new attachment is generated, so that the relevant crop only applies in the context it is edited. It also spares the user from dealing with the concept of various image sizes. (This does however have the potential of over-crowding the media-directory with differently cropped versions of images).

= Compatibility =

This add-on will work with:

* version 4 and up

== Installation ==

This add-on can be treated as both a WP plugin and a theme include.

= Plugin =
1. Copy the 'acf-image_crop' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

= Include =
1.	Copy the 'acf-image_crop' folder into your theme folder (can use sub folders). You can place the folder anywhere inside the 'wp-content' directory
2.	Edit your functions.php file and add the code below (Make sure the path is correct to include the acf-image_crop.php file)

`
add_action('acf/register_fields', 'my_register_fields');

function my_register_fields()
{
	include_once('acf-image-crop/acf-image-crop.php');
}
`

== Changelog ==

= 0.0.5 =
* Initial Release.
