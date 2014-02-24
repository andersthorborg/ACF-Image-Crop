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
			'save_format' => 'object',
			'target_size' => 'thumbnail'
			// add default here to merge into your field.
			// This makes life easy when creating the field options as you don't need to use any if( isset('') ) logic. eg:
			//'preview_size' => 'thumbnail'
		);

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
				'object'	=>	__("Image Object",'acf'),
				'url'		=>	__("Image URL",'acf'),
				'id'		=>	__("Image ID",'acf')
			)
		));
		?>
	</td>
</tr>
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
			'value'   =>  $field['target_size'],
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
		$originalImage = null;
		if( $data && is_object($data) && is_numeric($data->original_image) )
		{
			$originalImage = wp_get_attachment_image_src($data->original_image, 'full');
			$url = wp_get_attachment_image_src($data->original_image, $field['preview_size']);
			if(is_numeric($data->cropped_image)){				
				$url = wp_get_attachment_image_src($data->cropped_image, $field['preview_size']);	
			}						
			$o['class'] = 'active';
			$o['url'] = $url[0];			
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
		?>
<div class="acf-image-uploader clearfix <?php echo $o['class']; ?>" data-preview_size="<?php echo $field['preview_size']; ?>" data-library="<?php echo $field['library']; ?>" data-width="<?php echo $width ?>" data-height="<?php echo $height ?>" data-crop-type="<?php echo $field['crop_type'] ?>" <?php echo ($field['force_crop'] == 'yes' ? 'data-force-crop="true"' : '') ?> >
	<input class="acf-image-value" data-original-image="<?php echo $data->original_image ?>" data-cropped-image="<?php echo $data->cropped_image ?>" type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo htmlspecialchars($field['value']); ?>" />	
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
				<?php if ($originalImage ): ?>
					<img class="crop-image" src="<?php echo $originalImage[0] ?>" data-width="<?php echo $originalImage[1] ?>" data-height="<?php echo $originalImage[2] ?>" alt="">
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
		if(!is_object($data)){
			return $value;
		}

		$value = $data->cropped_image;
		
		// format
		if( $field['save_format'] == 'url' )
		{
			$value = wp_get_attachment_url( $data->cropped_image );
		}
		elseif( $field['save_format'] == 'object' )
		{
			$attachment = get_post( $data->cropped_image );
			
			
			// validate
			if( !$attachment )
			{
				return false;	
			}
			
			
			// create array to hold value data
			$src = wp_get_attachment_image_src( $attachment->ID, 'full' );
			
			$value = array(
				'id' => $attachment->ID,
				'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content,
				'mime_type'	=> $attachment->post_mime_type,
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
					$value[ 'sizes' ][ $image_size ] = $src[0];
					$value[ 'sizes' ][ $image_size . '-width' ] = $src[1];
					$value[ 'sizes' ][ $image_size . '-height' ] = $src[2];
				}
				// foreach( $image_sizes as $image_size )
			}
			// if( $image_sizes )
			
		}		
		return $value;
		
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
                wp_register_script('acf-input-image_crop', $this->settings['dir'] . 'js/input.js', array('acf-input', 'imgareaselect'), $this->settings['version']);

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
    	wp_register_script('acf-input-image-crop-options', $this->settings['dir'] . 'js/options.js', array('jquery'), $this->settings['version']);
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

	function perform_crop(){
		$targetWidth = $_POST['target_width'];
		$targetHeight = $_POST['target_height'];		
		$id = $this->generate_cropped_image($_POST['id'], $_POST['x1'], $_POST['x2'], $_POST['y1'], $_POST['y2'], $targetWidth, $targetHeight);
		$previewUrl = wp_get_attachment_image_src( $id, $_POST['preview_size']);
		$fullUrl = wp_get_attachment_image_src( $id, 'full');
		$data = array(
			'id' => $id,
			'url_preview' => $previewUrl[0],
			'url_full' => $fullUrl[0]
		);		
		echo json_encode($data);
		die();
	}

	function generate_cropped_image($id, $x1, $x2, $y1, $y2, $targetW, $targetH){//$id, $x1, $x2, $y$, $y2, $targetW, $targetH){
		require_once ABSPATH . "/wp-admin/includes/file.php";
        require_once ABSPATH . "/wp-admin/includes/image.php";                
                        
        $filename = wp_crop_image($id, $x1, $y1, $x2 - $x1, $y2 - $y1, $targetW, $targetH);   
        // GENERATE NEW ATTACHMENT FROM NEW FILE
        $wp_filetype = wp_check_filetype(basename($filename), null );              
        $attachment = array(
             'guid' => $filename, 
             'post_mime_type' => $wp_filetype['type'],
             'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
             'post_content' => '',
             'post_status' => 'inherit'
          );                            
        $attach_id = wp_insert_attachment( $attachment, $filename);                  
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        // Return ID of cropped image
        return $attach_id;        
	}

}


// create field
new acf_field_image_crop();

?>
