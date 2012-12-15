ACF-Image-Crop
==============

Basic proof of concept of an ACF image field with user-crop. It still needs a lot of testing.

When setting up the field, you are able to pre-define dimensions/aspect ratio, that the user can crop the image to right in the displayed image.
This field is an extension of the original image-field, and should behave in the same way as the original image field except for the cropping option.

Installation
==============
1. Create a folder called "fields" in your theme folder (if not already created) and copy the image-crop.php and lib-folder into it.*

2. Add the following code to your functions.php:

if( function_exists( 'register_field' ) ){
    register_field('ImageCrop', dirname(__File__) . '/fields/image_crop.php');
}

*If you choose to use another location for the field, be sure to set the "pathToFields"-variable of image_crop.php in line 36 correspondingly

Usage
==============
When setting up a field, chose the crop type you would like to use:

Fixed: the aspect ratio is locked to the specified dimensions. The user is able to select a bigger area, which will be scaled down to the specified dimensions after the crop.
Variable height/width: One dimension is fixed to the specified size, while the other can be selected between the min and max specified. Leave blank for no min/max.
Free: The user can freely crop the image as desired.

In the edit screens when an image is selected, a crop button will appear after the image. When pressed the image can be cropped based on the selected settings.
When the post is saved, the image is cropped based on the users cropping.
If the user doesn't crop the image, no changes will be made.

