=== Advanced Custom Fields: Image Crop Add-on ===
Contributors: andersthorborg
Tags: afc, advanced custom fields, image crop, image, crop
Requires at least: 3.5
Tested up to: 3.9.1
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An image field making it possible/required for the user to crop the selected image to the specified image size or dimensions

== Description ==

ACF image crop is an extended version of the native Image-field in ACF.
The field gives the developer/administrator the option to predefine a size for the image, which the user is prompted to crop on the various edit screens. This solves the common issue of images being cropped inappropriately by the automated center-crop, that wordpress performs.

The plugin supports the defined image sizes as well as a custom option, enabling the developer to specify the dimensions from within the field edit screen.

The field can be configured to enforce a hard crop or a minimal-dimension-based crop. The hard crop will lock the aspect ratio of the crop where as the minimal-dimension-based crop will not allow the user to crop the image below the specified dimensions.

This plugin diverts from plugins like [Manual Image Crop](http://wordpress.org/plugins/manual-image-crop/) in that when the user crops an image, a new attachment is generated, so that the relevant crop only applies in the context it is edited. It also keeps the user from dealing with the concept of various image sizes.

As of version 1.0 the field can be configured to either create the cropped image as a media-item (the default behavior) or simply create it and refer directly to the file without adding it to the media library. This will prevent the media library from being cluttered with several cropped versions of the same image. When this option is selected the only available return type for the field is URL.

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

== Screenshots ==

1. Use a registered image size as the field target size
2. Or use custom dimensions on the fly
3. On the edit screen, select/upload an image as usual
4. A crop-button will appear beneath the image (If desired, use the "Force user crop"-option to initialize the crop as soon as the user selects the image)
5. The image is cropped to the desired format, using the restrictions set under field options
6. The new format is shown using the specified preview size. The original image is kept with the field, so the image can be re-cropped at any time.


== Changelog ==

= 1.1.1 =
* Removed unsued references and that caused php warnings
* Added a missing default value that caused a php warning

= 1.1 =
* Added ACF5 compatibility.
* Please report any compatibility issues. As this has been an urgent feature request I have not had as much time for testing as I would have liked.

= 1.0 =
* Added option to save the image to media library or refer directly to the created image, not using the media library.
* Added better compatibility with the native image field making it possible to migrate from the regular image field to the crop-image field without losing the images currently attached. (It doesn't work the other way around)

= 0.8 =
* Fixed an issue resulting in a black image, when image was cropped without moving the crop handles

= 0.7 =
* Fixed return types other than image id causing fatal error

= 0.6 =
* Fix for WP installs with non-standard folder structures

= 0.5 =
* Initial Release.

