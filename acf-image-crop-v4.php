<?php

class acf_field_image_crop extends acf_field_image
{
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options


	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/

	function __construct()
	{
		// vars
		$this->name = 'image_crop';
		$this->label = __('Image with user-crop');
		$this->category = __("Content",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'force_crop' => 'no',
			'crop_type' => 'hard',
			'preview_size' => 'medium',
			'save_format' => 'id',
			'save_in_media_library' => 'yes',
			'target_size' => 'thumbnail',
			'retina_mode' => 'no'
			// add default here to merge into your field.
			// This makes life easy when creating the field options as you don't need to use any if( isset('') ) logic. eg:
			//'preview_size' => 'thumbnail'
		);

		$this->options = get_option( 'acf_image_crop_settings' );

		//Call grandparent cunstructor
		acf_field::__construct();

    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);

		// add ajax action to be able to retrieve full image size via javascript
        add_action( 'wp_ajax_acf_image_crop_get_image_size', array( &$this, 'crop_get_image_size' ) );
        add_action( 'wp_ajax_acf_image_crop_perform_crop', array( &$this, 'perform_crop' ) );

        // add filter to media query function to hide cropped images from media library
        add_filter('ajax_query_attachments_args', array($this, 'filterMediaQuery'));

        // Register extra fields on the media settings page on admin_init
        add_action('admin_init', array($this, 'registerSettings'));

	}

	// AJAX handler for retieving full image dimensions from ID
	public function crop_get_image_size()
    {
        $img = wp_get_attachment_image_src( $_POST['image_id'], 'full');
        if($img){
            echo json_encode( array(
            		'url' => $img[0],
                    'width' => $img[1],
                    'height' => $img[2]
                ) );
            }
        exit;
    }


	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function create_options($field)
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/

		// key is needed in the field names to correctly save the data
		$key = $field['name'];


		// Create Field Options HTML
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Crop type", 'acf'); ?></label>
		<p class="description"><?php _e("Select the type of crop the user should perform", 'acf'); ?></p>
	</td>
	<td>
		<?php

		do_action('acf/create_field', array(
			'type'    =>  'select',
			'name'    =>  'fields[' . $key . '][crop_type]',
			'value'   =>  $field['crop_type'],
			'class'   =>  'crop-type-select',
			'choices' 	=>	array(
				'hard' => __('Hard crop', 'acf'),
				'min' => __('Minimal dimensions', 'acf')
			)
		));

		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Target Size", 'acf'); ?></label>
		<p class="description"><?php _e("Select the target size for this field", 'acf'); ?></p>
	</td>
	<td>
		<?php
		$sizes = array_merge(apply_filters('acf/get_image_sizes', array()), array('custom' => __("Custom size",'acf')));
		unset($sizes['full']);
		do_action('acf/create_field', array(
			'type'    =>  'select',
			'name'    =>  'fields[' . $key . '][target_size]',
			'value'   =>  $field['target_size'],
			'class'	  =>  'target-size-select',
			'choices' 	=>	$sizes
		));

		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?> dimensions-wrap	<?php echo ($field['target_size'] == 'custom' ? '' : 'hidden') ?>">
	<td class="label">
		<label><?php _e("Dimensions", 'acf'); ?></label>
		<p class="description"><span class="dimensions-description <?php echo ($field['crop_type'] != 'hard' ? 'hidden' : '') ?>" data-type="hard"><?php _e("Enter the dimensions for the image.", 'acf'); ?></span><span class="dimensions-description <?php echo ($field['crop_type'] != 'min' ? 'hidden' : '') ?>" data-type="min"><?php _e("Enter the minimum dimensions for the image. Leave fields blank for no minimum requirement.", 'acf'); ?></span></p>
	</td>
	<td>
		<?php

		do_action('acf/create_field', array(
			'type'    =>  'text',
			'name'    =>  'fields[' . $key . '][width]',
			'value'   =>  $field['width'],
			'class'	  =>  'width dimension',
			'placeholder' => 'Width'
		));
		?>
		<div class="acf-input-wrap">&nbsp;:&nbsp;</div>
		<?php
		do_action('acf/create_field', array(
			'type'    =>  'text',
			'name'    =>  'fields[' . $key . '][height]',
			'value'   =>  $field['height'],
			'class'	  =>  'height dimension',
			'placeholder' => 'Height'
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Preview Size", 'acf'); ?></label>
		<p class="description"><?php _e("Select the target size for this field", 'acf'); ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'    =>  'select',
			'name'    =>  'fields[' . $key . '][preview_size]',
			'value'   =>  $field['preview_size'],
			'class'	  =>  'preview-size-select',
			'choices' 	=>	apply_filters('acf/get_image_sizes', array())
		));

		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Force crop",'acf'); ?></label>
		<p><?php _e("Force the user to crop the image as soon at it is selected.",'acf') ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][force_crop]',
			'value'		=>	$field['force_crop'],
			'layout'	=>	'horizontal',
			'choices'	=> array(
				'yes'	=>	__("Yes",'acf'),
				'no'		=>	__("No",'acf')
			)
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Save cropped image in media library",'acf'); ?></label>
		<p><?php _e("If the cropped image is not saved in the media library, the \"Image URL\" is the only available return value.", 'acf') ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][save_in_media_library]',
			'value'		=>	$field['save_in_media_library'],
			'layout'	=>	'horizontal',
			'choices'	=> array(
				'yes'	=>	__("Yes",'acf'),
				'no'		=>	__("No",'acf')
			),
			'class'		=>  'save-in-media-library-select'
		));
		?>
	</td>
</tr>
<?php
	$retina_instructions = __('Require and crop double the size set for this image. Enable this if you are using plugins like WP Retina 2x.','acf-image_crop');
	if($this->getOption('retina_mode')){
	    $retina_instructions .= '<br>' . __('NB. You currently have enabled retina mode globally for all fields through <a href="' . admin_url('options-media.php') . '#acf-image-crop-retina-mode' . '">settings</a>, which will override this setting.','acf-image_crop');
	}
?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e('Retina/@2x mode ','acf-image_crop'); ?></label>
		<p><?php echo $retina_instructions ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][retina_mode]',
			'value'		=>	$field['retina_mode'],
			'layout'	=>	'horizontal',
			'choices'	=> array(
				'yes'	=>	__("Yes",'acf'),
				'no'		=>	__("No",'acf')
			),
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Return Value",'acf'); ?></label>
		<p><?php _e("Specify the returned value on front end",'acf') ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][save_format]',
			'value'		=>	$field['save_format'],
			'layout'	=>	'horizontal',
			'choices'	=> array(
				'url'		=>	__("Image URL",'acf'),
				'id'		=>	__("Image ID",'acf'),
				'object'	=>	__("Image Object",'acf')
			),
			'class'		=> 	'return-value-select'
		));
		?>
	</td>
</tr>
		<?php

	}


	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function create_field( $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		// perhaps use $field['preview_size'] to alter the markup?
		$data = json_decode($field['value']);
		// create Field HTML
				// vars
		$o = array(
			'class'		=>	'',
			'url'		=>	'',
		);
		// $originalImage = null;
		// if( $data && is_object($data) && is_numeric($data->original_image) )
		// {
		// 	$originalImage = wp_get_attachment_image_src($data->original_image, 'full');
		// 	$url = wp_get_attachment_image_src($data->original_image, $field['preview_size']);
		// 	if($field['save_in_media_library'] == 'yes'){
		// 		if(is_numeric($data->cropped_image)){
		// 			$url = wp_get_attachment_image_src($data->cropped_image, $field['preview_size']);
		// 		}
		// 	}
		// 	else{
		// 		if($data->cropped_image_url){
		// 			$url = $data->cropped_image_url;
		// 		}
		// 		else{

		// 		}
		// 	}

		// 	$o['class'] = 'active';
		// 	$o['url'] = $url[0];
		// }
		$imageData = $this->get_image_data($field);
		//print_r($field);
		$originalImage = wp_get_attachment_image_src($imageData->original_image, 'full');
		if($imageData->original_image){
			$o['class'] = 'active';
			$o['url'] = $imageData->preview_image_url;
		}
		$width = 0;
		$height = 0;
		if($field['target_size'] == 'custom'){
			$width = $field['width'];
			$height = $field['height'];
		}
		else{
			global $_wp_additional_image_sizes;
			$s = $field['target_size'];
			if (isset($_wp_additional_image_sizes[$s])) {
				$width = intval($_wp_additional_image_sizes[$s]['width']);
				$height = intval($_wp_additional_image_sizes[$s]['height']);
			} else {
				$width = get_option($s.'_size_w');
				$height = get_option($s.'_size_h');
			}
		}

		 // Retina mode
        if($this->getOption('retina_mode') || $field['retina_mode'] == 'yes'){
            $width = $width * 2;
            $height = $height * 2;
        }
		?>
<div class="acf-image-uploader clearfix acf-image-crop <?php echo $o['class']; ?>" data-field-id="<?php echo $field['key'] ?>" data-preview_size="<?php echo $field['preview_size']; ?>" data-library="<?php echo isset($field['library']) ? $field['library'] : 'all'; ?>" data-width="<?php echo $width ?>" data-height="<?php echo $height ?>" data-crop-type="<?php echo $field['crop_type'] ?>" <?php echo ($field['force_crop'] == 'yes' ? 'data-force-crop="true"' : '')?> data-save-to-media-library="<?php echo $field['save_in_media_library'] ?>"  >
	<input class="acf-image-value" data-original-image="<?php echo $imageData->original_image ?>"  data-cropped-image="<?php echo json_encode($imageData->cropped_image) ?>" type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo htmlspecialchars($field['value']); ?>" />
	<div class="has-image">
		<div class="image-section">
			<div class="hover">
				<ul class="bl">
					<li><a class="acf-button-delete ir" href="#"><?php _e("Remove",'acf'); ?></a></li>
					<li><a class="acf-button-edit ir" href="#"><?php _e("Edit",'acf'); ?></a></li>
				</ul>
			</div>
			<img class="acf-image-image" src="<?php echo $o['url']; ?>" alt=""/>
		</div>
		<div class="crop-section">
			<div class="crop-stage">
				<div class="crop-action">
					<h4>Crop the image</h4>
				<?php if ($imageData->original_image ): ?>
					<img class="crop-image" src="<?php echo $imageData->original_image_url ?>" data-width="<?php echo $imageData->original_image_width ?>" data-height="<?php echo $imageData->original_image_height ?>" alt="">
				<?php endif ?>
				</div>
				<div class="crop-preview">
					<h4>Preview</h4>
					<div class="preview"></div>
					<div class="crop-controls">
						<a href="#" class="button button-large cancel-crop-button">Cancel</a> <a href="#" class="button button-large button-primary perform-crop-button">Crop!</a>
					</div>
				</div>
				<!-- <img  src="<?php echo $o['url']; ?>" alt=""/> -->
			</div>
			<a href="#" class="button button-large init-crop-button">Crop</a>
		</div>
	</div>
	<div class="no-image">
		<p><?php _e('No image selected','acf'); ?> <input type="button" class="button add-image" value="<?php _e('Add Image','acf'); ?>" />
	</div>
</div>
		<?php
	}



	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/

	function format_value_for_api( $value, $post_id, $field )
	{
		// validate
		if( !$value )
		{
			return false;
		}
		$data = json_decode($value);
		if(is_object($data)){
			$value = $data->cropped_image;
		}
		else{
			// We are migrating from a standard image field
			$data = new stdClass();
			$data->cropped_image = $value;
			$data->original_image = $value;
		}

		// format
		if( $field['save_format'] == 'url' )
		{
			if(is_numeric($data->cropped_image)){
				$value = wp_get_attachment_url( $data->cropped_image );
			}
			elseif(is_array($data->cropped_image)){

				$value = $this->getAbsoluteImageUrl($data->cropped_image['image']);
			}
			elseif(is_object($data->cropped_image)){
				$value = $this->getAbsoluteImageUrl($data->cropped_image->image);
			}

		}
		elseif( $field['save_format'] == 'object' )
		{
			if(is_numeric($data->cropped_image )){
				$value = $this->getImageArray($data->cropped_image);
                $value['original_image'] = $this->getImageArray($data->original_image);
				// $attachment = get_post( $data->cropped_image );
				// // validate
				// if( !$attachment )
				// {
				// 	return false;
				// }


				// // create array to hold value data
				// $src = wp_get_attachment_image_src( $attachment->ID, 'full' );

				// $value = array(
				// 	'id' => $attachment->ID,
				// 	'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				// 	'title' => $attachment->post_title,
				// 	'caption' => $attachment->post_excerpt,
				// 	'description' => $attachment->post_content,
				// 	'mime_type'	=> $attachment->post_mime_type,
				// 	'url' => $src[0],
				// 	'width' => $src[1],
				// 	'height' => $src[2],
				// 	'sizes' => array(),
				// );


				// // find all image sizes
				// $image_sizes = get_intermediate_image_sizes();

				// if( $image_sizes )
				// {
				// 	foreach( $image_sizes as $image_size )
				// 	{
				// 		// find src
				// 		$src = wp_get_attachment_image_src( $attachment->ID, $image_size );

				// 		// add src
				// 		$value[ 'sizes' ][ $image_size ] = $src[0];
				// 		$value[ 'sizes' ][ $image_size . '-width' ] = $src[1];
				// 		$value[ 'sizes' ][ $image_size . '-height' ] = $src[2];
				// 	}
				// 	// foreach( $image_sizes as $image_size )
				// }
			}
			 elseif(is_array( $data->cropped_image) || is_object($data->cropped_image)){
                // Cropped image is not saved to media directory. Get data from original image instead
                $value = $this->getImageArray($data->original_image);

                // Get the relative url from data
                $relativeUrl  = '';
                if(is_array( $data->cropped_image)){
                    $relativeUrl = $data->cropped_image['image'];
                }
                else{
                    $relativeUrl = $data->cropped_image->image;
                }

                // Replace URL with cropped version
                $value['url'] = $this->getAbsoluteImageUrl($relativeUrl);

                // Calculate and replace sizes
                $imagePath = $this->getImagePath($relativeUrl);
                $dimensions = getimagesize($imagePath);
                $value['width'] = $dimensions[0];
                $value['height'] = $dimensions[1];

                // Add original image info
                $value['original_image'] = $this->getImageArray($data->original_image);
            }
			else{

				//echo 'ELSE';
			}

		}
		return $value;

	}


	function getImageArray($id){
        $attachment = get_post( $id );
        // validate
        if( !$attachment )
        {
            return false;
        }


        // create array to hold value data
        $src = wp_get_attachment_image_src( $attachment->ID, 'full' );

        $imageArray = array(
            'id' => $attachment->ID,
            'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
            'title' => $attachment->post_title,
            'caption' => $attachment->post_excerpt,
            'description' => $attachment->post_content,
            'mime_type' => $attachment->post_mime_type,
            'url' => $src[0],
            'width' => $src[1],
            'height' => $src[2],
            'sizes' => array(),
        );


        // find all image sizes
        $image_sizes = get_intermediate_image_sizes();

        if( $image_sizes )
        {
            foreach( $image_sizes as $image_size )
            {
                // find src
                $src = wp_get_attachment_image_src( $attachment->ID, $image_size );

                // add src
                $imageArray[ 'sizes' ][ $image_size ] = $src[0];
                $imageArray[ 'sizes' ][ $image_size . '-width' ] = $src[1];
                $imageArray[ 'sizes' ][ $image_size . '-height' ] = $src[2];
            }
        }
        return $imageArray;
    }

	/*
        *  input_admin_enqueue_scripts()
        *
        *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
        *  Use this action to add css + javascript to assist your create_field() action.
        *
        *  $info        http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
        *  @type        action
        *  @since        3.6
        *  @date        23/01/13
        */

        function input_admin_enqueue_scripts()
        {
                // Note: This function can be removed if not used


                // register acf scripts
                wp_register_script('acf-input-image_crop', $this->settings['dir'] . 'js/input-v4.js', array('acf-input', 'imgareaselect'), $this->settings['version']);

                wp_register_style('acf-input-image_crop', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version']);
                wp_register_script( 'jcrop', includes_url( 'js/jcrop/jquery.Jcrop.min.css' ));


                // scripts
                wp_enqueue_script(array(
                        'acf-input-image_crop'
                ));

                wp_localize_script( 'acf-input-image_crop', 'ajax', array('nonce' => wp_create_nonce('acf_nonce')) );

                // styles
                wp_enqueue_style(array(
                        'acf-input-image_crop',
                        'imgareaselect'
                ));

    }


    /*
    *  field_group_admin_enqueue_scripts()
    *
    *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
    *  Use this action to add css + javascript to assist your create_field_options() action.
    *
    *  $info        http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
    *  @type        action
    *  @since        3.6
    *  @date        23/01/13
    */

    function field_group_admin_enqueue_scripts()
    {
        // Note: This function can be removed if not used
    	wp_register_script('acf-input-image-crop-options', $this->settings['dir'] . 'js/options-v4.js', array('jquery'), $this->settings['version']);
    	wp_enqueue_script( 'acf-input-image-crop-options');

    	wp_register_style('acf-input-image-crop-options', $this->settings['dir'] . 'css/options.css');
    	wp_enqueue_style( 'acf-input-image-crop-options');
    }


	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/

	function update_value( $value, $post_id, $field )
	{
		// array?
		if( is_array($value) && isset($value['id']) )
		{
			$value = $value['id'];
		}

		// object?
		if( is_object($value) && isset($value->ID) )
		{
			$value = $value->ID;
		}

		return $value;
	}

	function get_image_data($field){
		$imageData = new stdClass();
		$imageData->original_image = '';
		$imageData->original_image_width = '';
		$imageData->original_image_height = '';
		$imageData->cropped_image = '';
		$imageData->original_image_url = '';
		$imageData->preview_image_url = '';
		$imageData->image_url = '';

		if($field['value'] == ''){
			// Field has not yet been saved or is an empty image field
			return $imageData;
		}

		$data = json_decode($field['value']);

		if(! is_object($data)){
			// Field was saved as a regular image field
			$imageAtts = wp_get_attachment_image_src($field['value'], 'full');
			$imageData->original_image = $field['value'];
			$imageData->original_image_width = $imageAtts[1];
			$imageData->original_image_height = $imageAtts[2];
			$imageData->preview_image_url = $this->get_image_src($field['value'], $field['preview_size']);
			$imageData->image_url = $this->get_image_src($field['value'], 'full');
			$imageData->original_image_url = $imageData->image_url;
			return $imageData;
		}

		if( !is_numeric($data->original_image) )
		{
			// The field has been saved, but has no image
			return $imageData;
		}

		// By now, we have at least a saved original image
		$imageAtts = wp_get_attachment_image_src($data->original_image, 'full');
		$imageData->original_image = $data->original_image;
		$imageData->original_image_width = $imageAtts[1];
		$imageData->original_image_height = $imageAtts[2];
		$imageData->original_image_url = $this->get_image_src($data->original_image, 'full');

		// Set defaults to original image
		$imageData->image_url = $this->get_image_src($data->original_image, 'full');
		$imageData->preview_image_url = $this->get_image_src($data->original_image, $field['preview_size']);

		// Check if there is a cropped version and set appropriate attributes
		if(is_numeric($data->cropped_image)){
			// Cropped image was saved to media library ans has an ID
			$imageData->cropped_image = $data->cropped_image;
			$imageData->image_url = $this->get_image_src($data->cropped_image, 'full');
			$imageData->preview_image_url = $this->get_image_src($data->cropped_image, $field['preview_size']);
		}
		elseif(is_object($data->cropped_image)){
			// Cropped image was not saved to media library and is only stored by its URL
			$imageData->cropped_image = $data->cropped_image;

			// Generate appropriate URLs
			$mediaDir = wp_upload_dir();
			$imageData->image_url = $mediaDir['baseurl'] . '/' .  $data->cropped_image->image;
			$imageData->preview_image_url = $mediaDir['baseurl'] . '/' . $data->cropped_image->preview;
		}
		return $imageData;
	}

	function perform_crop(){
		$targetWidth = $_POST['target_width'];
		$targetHeight = $_POST['target_height'];
		$saveToMediaLibrary = $_POST['save_to_media_library'] == 'yes';
		$imageData = $this->generate_cropped_image($_POST['id'], $_POST['x1'], $_POST['x2'], $_POST['y1'], $_POST['y2'], $targetWidth, $targetHeight, $saveToMediaLibrary, $_POST['preview_size']);
		// $previewUrl = wp_get_attachment_image_src( $id, $_POST['preview_size']);
		// $fullUrl = wp_get_attachment_image_src( $id, 'full');
		echo json_encode($imageData);
		die();
	}

	function generate_cropped_image($id, $x1, $x2, $y1, $y2, $targetW, $targetH, $saveToMediaLibrary, $previewSize){//$id, $x1, $x2, $y$, $y2, $targetW, $targetH){
		require_once ABSPATH . "/wp-admin/includes/file.php";
        require_once ABSPATH . "/wp-admin/includes/image.php";

        // Create the variable that will hold the new image data
        $imageData = array();

        // Fetch media library info
        $mediaDir = wp_upload_dir();

        // Get original image info
        $originalImageData = wp_get_attachment_metadata($id);

        // Get image editor from original image path to crop the image
        $image = wp_get_image_editor( $mediaDir['basedir'] . '/' . $originalImageData['file'] );

        if(! is_wp_error( $image ) ){

        	// Crop the image using the provided measurements
        	$image->crop($x1, $y1, $x2 - $x1, $y2 - $y1, $targetW, $targetH);

	        // Retrieve original filename and seperate it from its file extension
	        $originalFileName = explode('.', basename($originalImageData['file']));

	        // Retrieve and remove file extension from array
	        $originalFileExtension = array_pop($originalFileName);

	        // Generate new base filename
	        $targetFileName = implode('.', $originalFileName) . '_' . $targetW . 'x' . $targetH . '_acf_cropped'  . '.' . $originalFileExtension;

	        // Generate target path new file using existing media library
	        $targetFilePath = $mediaDir['path'] . '/' . wp_unique_filename( $mediaDir['path'], $targetFileName);

	        // Get the relative path to save as the actual image url
	        $targetRelativePath = str_replace($mediaDir['basedir'] . '/', '', $targetFilePath);

	        // Save the image to the target path
	        if(is_wp_error($image->save($targetFilePath))){
	        	// There was an error saving the image
	        	//TODO handle it
	        }

	        // If file should be saved to media library, create an attachment for it at get the new attachment ID
	        if($saveToMediaLibrary){
	        	// Generate attachment from created file

	        	// Get the filetype
		        $wp_filetype = wp_check_filetype(basename($targetFilePath), null );
		        $attachment = array(
		             'guid' => $mediaDir['url'] . '/' . basename($targetFilePath),
		             'post_mime_type' => $wp_filetype['type'],
		             'post_title' => preg_replace('/\.[^.]+$/', '', basename($targetFilePath)),
		             'post_content' => '',
		             'post_status' => 'inherit'
	          	);
		        $attachmentId = wp_insert_attachment( $attachment, $targetFilePath);
		        $attachmentData = wp_generate_attachment_metadata( $attachmentId, $targetFilePath );
		        wp_update_attachment_metadata( $attachmentId, $attachmentData );
		        add_post_meta($attachmentId, 'acf_is_cropped', 'true', true);

		        // Add the id to the imageData-array
		        $imageData['value'] = $attachmentId;

		        // Add the image url
		        $imageUrlObject = wp_get_attachment_image_src( $attachmentId, 'full');
		        $imageData['url'] = $imageUrlObject[0];

		        // Add the preview url as well
		        $previewUrlObject = wp_get_attachment_image_src( $attachmentId, $previewSize);
		        $imageData['preview_url'] = $previewUrlObject[0];
	        }
	        // Else we need to return the actual path of the cropped image
	        else{
	        	// Add the image url to the imageData-array
	        	$imageData['value'] = array('image' => $targetRelativePath);
	        	$imageData['url'] = $mediaDir['baseurl'] . '/' . $targetRelativePath;

	    		// Get preview size dimensions
	        	global $_wp_additional_image_sizes;
	        	$previewWidth = 0;
	        	$previewHeight = 0;
	        	$crop = 0;
				if (isset($_wp_additional_image_sizes[$previewSize])) {
					$previewWidth = intval($_wp_additional_image_sizes[$previewSize]['width']);
					$previewHeight = intval($_wp_additional_image_sizes[$previewSize]['height']);
					$crop = $_wp_additional_image_sizes[$previewSize]['crop'];
				} else {
					$previewWidth = get_option($previewSize.'_size_w');
					$previewHeight = get_option($previewSize.'_size_h');
					$crop = get_option($previewSize.'_crop');
				}

	        	// Generate preview file path
	        	$previewFilePath = $mediaDir['path'] . '/' . wp_unique_filename( $mediaDir['path'], 'preview_' . $targetFileName);
	        	$previewRelativePath = str_replace($mediaDir['basedir'] . '/', '', $previewFilePath);

	        	// Get image editor from cropped image
	        	$croppedImage = wp_get_image_editor( $targetFilePath );
	        	$croppedImage->resize($previewWidth, $previewHeight, $crop);

	        	// Save the preview
	        	$croppedImage->save($previewFilePath);

	        	// Add the preview url
		        $imageData['preview_url'] = $mediaDir['baseurl'] . '/' . $previewRelativePath;
		        $imageData['value']['preview'] = $previewRelativePath;
	        }
	        $imageData['success'] = true;
	        return $imageData;
	    }
	    else{
	    	 // Handle WP_ERROR
	        $response = array();
	        $response['success'] = false;
	        $response['error_message'] = '';
	        foreach($image->error_data as $code => $message){
	            $response['error_message'] .= '<p><strong>' . $code . '</strong>: ' . $message . '</p>';
	        }
	        return $response;
	    }
	}

	function get_image_src($id, $size = 'thumbnail'){
		$atts = wp_get_attachment_image_src( $id, $size);
		return $atts[0];
	}

	function getAbsoluteImageUrl($relativeUrl){
		$mediaDir = wp_upload_dir();
		return $mediaDir['baseurl'] . '/' .  $relativeUrl;
	}

	function getImagePath($relativePath){
        $mediaDir = wp_upload_dir();
        return $mediaDir['basedir'] . '/' .  $relativePath;
    }

	function filterMediaQuery($args){
        // get options
        $options = get_option( 'acf_image_crop_settings' );
        $hide = $options['hide_cropped'];

        // If hide option is enabled, do not select items with the acf_is_cropped meta-field
        if($hide){
            $args['meta_query']= array(
                array(
                    'key' => 'acf_is_cropped',
                    'compare' => 'NOT EXISTS'
                )
            );
        }
        return $args;
    }


    function registerSettings(){
        add_settings_section(
            'acf_image_crop_settings',
            __('ACF Image Crop Settings','acf-image_crop'),
            array($this, 'displayImageCropSettingsSection'),
            'media'
        );

        register_setting(
            'media',                                       // settings page
            'acf_image_crop_settings'                     // option name
        );

        add_settings_field(
            'acf_image_crop_hide_cropped',      // id
            __('Hide cropped images from media dialog', 'acf-image_crop'),              // setting title
            array($this, 'displayHideFromMediaInput'),    // display callback
            'media',                 // settings page
            'acf_image_crop_settings'                  // settings section
        );

        add_settings_field(
            'acf_image_crop_retina_mode',      // id
            __('Enable global retina mode (beta)', 'acf-image_crop'),              // setting title
            array($this, 'displayRetinaModeInput'),    // display callback
            'media',                 // settings page
            'acf_image_crop_settings'                  // settings section
        );
    }

    function displayHideFromMediaInput(){
        // Get plugin options
        $options = get_option( 'acf_image_crop_settings' );
        $value = $options['hide_cropped'];

        // echo the field
        ?>
    <input name='acf_image_crop_settings[hide_cropped]'
     type='checkbox' <?php echo $value ? 'checked' :  '' ?> value='true' />
        <?php
    }

    function displayRetinaModeInput(){
        // Get plugin options
        $options = get_option( 'acf_image_crop_settings' );
        $value = $options['retina_mode'];

        // echo the field
        ?>
    <input id="acf-image-crop-retina-mode" name='acf_image_crop_settings[retina_mode]'
     type='checkbox' <?php echo $value ? 'checked' :  '' ?> value='true' />
        <?php
    }

    function displayImageCropSettingsSection(){
        echo '';
    }

    function getOption($key){
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

}


// create field
new acf_field_image_crop();

?>
