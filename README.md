# Advanced Custom Fields: Image Crop Add-on #
Contributors: andersthorborg
Tags: afc, advanced custom fields, image crop, image, crop
Requires at least: 3.5
Tested up to: 4.9.4
Stable tag: 1.4.12
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An image field making it possible/required for the user to crop the selected image to the specified image size or dimensions

## Description ##

ACF image crop is an extended version of the native Image-field in ACF.
The field gives the developer/administrator the option to predefine a size for the image, which the user is prompted to crop on the various edit screens. This solves the common issue of images being cropped inappropriately by the automated center-crop, that wordpress performs.

The plugin supports the defined image sizes as well as a custom option, enabling the developer to specify the dimensions from within the field edit screen.

The field can be configured to enforce a hard crop or a minimal-dimension-based crop. The hard crop will lock the aspect ratio of the crop where as the minimal-dimension-based crop will not allow the user to crop the image below the specified dimensions.

This plugin diverts from plugins like [Manual Image Crop](http://wordpress.org/plugins/manual-image-crop/) in that when the user crops an image, a new attachment is generated, so that the relevant crop only applies in the context it is edited. It also keeps the user from dealing with the concept of various image sizes.

As of version 1.0 the field can be configured to either create the cropped image as a media-item (the default behavior) or simply create it and refer directly to the file without adding it to the media library. This will prevent the media library from being cluttered with several cropped versions of the same image. When this option is selected the only available return type for the field is URL.

### Compatibility ###

This add-on will work with:

* version 4 and up

## Installation ##

This add-on can be treated as both a WP plugin and a theme include.

### Plugin ###
1. Copy the 'acf-image_crop' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

### Include ###
1.	Copy the 'acf-image_crop' folder into your theme folder (can use sub folders). You can place the folder anywhere inside the 'wp-content' directory
2.	Edit your functions.php file and add the code below (Make sure the path is correct to include the acf-image_crop.php file)

`
add_action('acf/register_fields', 'my_register_fields');

function my_register_fields()
{
	include_once('acf-image-crop/acf-image-crop.php');
}
`

## Screenshots ##

1. Use a registered image size as the field target size
2. Or use custom dimensions on the fly
3. On the edit screen, select/upload an image as usual
4. A crop-button will appear beneath the image (If desired, use the "Force user crop"-option to initialize the crop as soon as the user selects the image)
5. The image is cropped to the desired format, using the restrictions set under field options
6. The new format is shown using the specified preview size. The original image is kept with the field, so the image can be re-cropped at any time.


## Changelog ##

### 1.4.13 ###
* Fix - applied max width on image previews
* Tested compatibility up to 4.9.4

### 1.4.12 ###
* Fix compatibility with ACF Pro 5.6.0

### 1.4.11 ###
* Address issue with changed ACF Pro validation behavior causing php warnings when saving fields

### 1.4.10 ###
* Add compatibility with ACF Pro 5.5.5

### 1.4.9 ###
* Use acf-image-crop/filename_postfix to allow custom filename postfixes

### 1.4.8 ###
* Fix button styling
* Prevent php warnings for unset field settings

### 1.4.7 ###
* Added compatibility with ACF PRO 5.4.2.2 icons

### 1.4.6 ###
* Added compatibility with ACF 4.4.2 and ACF PRO 5.2.9

### 1.4.5 ###
* Added compatibility with ACF 5.2.7
* Added image quality filter (needs testing)

### 1.4.4 ###
* Fixed migration from image field to not only return image ID
* Fixed a js error in field settings caused by a change in class names in ACF

### 1.4.3 ###
* Removed unused assets

### 1.4.2 ###
* Improved migration from standard field to ACF crop field
* Fixed and issue that caused warnings when options was not set
* Fixed crop preview not showing correct crop position in some browsers
* Improved error handling when server setup does not support image handling
* Fixed original image data missing when saving to media library in v4
* Improved error handling in v4

### 1.4.1 ###
* Fixed issue with image not cropping in v4

### 1.4 ###
* Fixed images with dot in the file name resulting in odd cropped image names
* Fixed issues with php notices in v4
* Fixed issues with broken image fields in v4
* Temporarily fixed images smaller than preview size not being added (ACF bug)
* Updated localization thanks to @tmconnect
* Various tweaks and fixes by @tmconnect

### 1.3 ###
* Updated to be compatible with original image field changes as of ACF Pro 5.0.8. IMPORTANT: As this is a quick fix to ensure compatability with the newest ACF PRO version it is not backwards compatible. If you are using ACF Pro 5.0.7 and below, please use version 1.2 of this add-on.

### 1.2 ###
* Improved: Edit image is now working for most cropped image fields.
* Fix: Wrong GUID for generated images that could cause issues when moving a site to a new location
* Tweak: Added "original_image"-attribute when using return type "Object", containing the original image data.
* Tweak: Return type "Object" is now available when not saving cropped image to media library. The data except from url, width and height is fetched from the original image.
* Feature: It is now possible to hide cropped images from the media dialog. (See the new settings section) NB.: Only works for future cropped images.
* Feature: Retina-mode, that makes the image field require and crop double the dimensions. Results in better integration with plugins like WP Retina 2x
* Feature: Settings-seciton under Settings -> Media. Here you can choose to hide cropped images from the media dialog as well as enable/disable global retina mode.

### 1.1.4 ###
* Fixed an issue causing a php warning when editing custom fields
* Fixed a js-issue causing image-crop-field hiding all subfields when editing repeater-/flexible content-fields

### 1.1.3 ###
* Fixed another issue with save to media option

### 1.1.2 ###
* Fixed issue with force crop option
* Fixed issue with save to media option
* Fixed issue with return type object

### 1.1.1 ###
* Removed unsued references and that caused php warnings
* Added a missing default value that caused a php warning

### 1.1 ###
* Added ACF5 compatibility.
* Please report any compatibility issues. As this has been an urgent feature request I have not had as much time for testing as I would have liked.

### 1.0 ###
* Added option to save the image to media library or refer directly to the created image, not using the media library.
* Added better compatibility with the native image field making it possible to migrate from the regular image field to the crop-image field without losing the images currently attached. (It doesn't work the other way around)

### 0.8 ###
* Fixed an issue resulting in a black image, when image was cropped without moving the crop handles

### 0.7 ###
* Fixed return types other than image id causing fatal error

### 0.6 ###
* Fix for WP installs with non-standard folder structures

### 0.5 ###
* Initial Release.

