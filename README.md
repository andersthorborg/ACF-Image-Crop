-----------------------

# ACF { Image Crop Add-on

Adds an 'Image with user-crop' field type for the [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) WordPress plugin.



-----------------------

### Overview
ACF image crop is an extended version of the native Image-field in ACF.
The field gives the developer/administrator the option to predefine a size for the image, which the user is prompted to crop on the various edit screens. This solves the common issue of images being cropped inappropriately by the automated center-crop, that wordpress performs.

The plugin supports the defined image sizes as well as a custom option, enabling the developer to specify the dimensions from within the field edit screen.

The field can be configured to enforce a hard crop or a minimal-dimension-based crop. The hard crop will lock the aspect ratio of the crop where as the minimal-dimension-based crop will not allow the user to crop the image below the specified dimensions.

This plugin diverts from plugins like [Manual Image Crop](http://wordpress.org/plugins/manual-image-crop/) in that when the user crops an image, a new attachment is generated, so that the relevant crop only applies in the context it is edited. It also spares the user from dealing with the concept of various image sizes.

As of version 1.0 the field can be configured to either create the cropped image as a media-item (the default behavior) or simply create it and refer directly to the file without adding it to the media library. This will prevent the media library from being cluttered with several cropped versions of the same image. When this option is selected the only available return type for the field is URL.



### Compatibility

This add-on will work with:

* version 4 and up

### Installation

This add-on can be treated as both a WP plugin and a theme include.

**Install as Plugin**

1. Copy the 'acf-image_crop' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

**Include within theme**

1.    Copy the 'acf-image_crop' folder into your theme folder (can use sub folders). You can place the folder anywhere inside the 'wp-content' directory
2.	Edit your functions.php file and add the code below (Make sure the path is correct to include the acf-image_crop.php file)

```php
include_once('acf-image_crop/acf-image_crop.php');
```

### Setup

The field has the same options as the standard image field. Besides from these, other options are available:

**Crop type**

Defines what kind of crop the user is going to perform. If the *Hard crop* option is selected, the aspect ratio of the selected size will be maintained.

The *Minimal dimensions* option uses the specified size as minima requirements. With this option, the user will not be able to crop the image below the specified dimensions. If a dimension is not specified, the user will be able to crop that dimension to any value.

**Target size**

The size, that the image will ultimately be cropped into. This list includes the currently defined image sizes as well as the custom option. If the custom option is selected, a *dimension* opition will become available asking for width:height.  NB. When using a custom target size along with the hard crop option **both width and height must be specified**.

**Force crop**

On the image field will be a *Crop*-button triggering the crop dialog. If the force crop option is enabled, the crop dialog will show up as soon as the user selects an image.

**Retina/@2x mode**

Makes the image field require double the dimensions selected. This makes it easier to integrate with plugins like WP Retina 2x.

###Further Details
Whenever the user selects an image, the original image is stored, so whenever the user decides to re-crop the image the user will see the image first selected, and not the cropped one. Every crop made creates a seperate media item, which allows for multiple uses of the same image wihtout overwriting previous crops.

The plugin settings can be found under Settings -> Media. Here you can enable global "Retina Mode" and choose to hide cropped images from the media dialog.

The plugin has only been tested with Chrome for Mac, and still needs a lot of work. Please let me know of any issues or feature requests.