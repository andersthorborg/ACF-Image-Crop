<?php

/*
 *	Advanced Custom Fields - New field template
 *	
 *	Create your field's functionality below and use the function:
 *	register_field($class_name, $file_path) to include the field
 *	in the acf plugin.
 *
 *	Documentation: 
 *
 */
 
 
class ImageCrop extends acf_Field
{

            
	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*	- This function is called when the field class is initalized on each page.
	*	- Here you can add filters / actions and setup any other functionality for your field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
		// do not delete!
    	parent::__construct($parent);
    	
            // set name / title
            $this->name = 'image_crop'; // variable name (no spaces / special characters / etc)
            $this->title = __("Image with user crop",'acf'); // field label (Displayed in edit screens)
            
            
            // add ajax action to be able to retrieve full image size via javascript
            add_action( 'wp_ajax_acf_crop_get_image_size', array( &$this, 'crop_get_image_size' ) );
            		
   	}
        
        public function crop_get_image_size()
	{		
                $img = wp_get_attachment_image_src( $_GET['image_id'], 'full');               
                if($img){
                    echo json_encode( array(
                            'width' => $img[1],
                            'height' => $img[2]
                        ) );
                    }
		exit;
	}

	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*	- this function is called from core/field_meta_box.php to create extra options
	*	for your field
	*
	*	@params
	*	- $key (int) - the $_POST obejct key required to save the options to the field
	*	- $field (array) - the field object
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{
		// vars
		$defaults = array(
			'save_format'	=>	'object',
			'preview_size'	=>	'large',
		);
		
		$field = array_merge($defaults, $field);
		
		?>
                <tr class="field_option field_option_<?php echo $this->name; ?> <?=  $field['key']?>_option">
			<td class="label">
				<label><?php _e("Return Value",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',                                        
					'name'	=>	'fields['.$key.'][save_format]',
					'value'	=>	$field['save_format'],
					'layout'	=>	'horizontal',
					'choices' => array(
						'object'	=>	__("Image Object",'acf'),
						'url'		=>	__("Image URL",'acf'),
						'id'		=>	__("Image ID",'acf')
					)
				));
				?>
			</td>
		</tr>
                <tr class="field_option field_option_<?php echo $this->name; ?> <?=  $field['key']?>_option">
			<td class="label">
				<label><?php _e("Preview Size",'acf'); ?></label>
			</td>
			<td>
				<?php
				
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][preview_size]',
					'value'	=>	$field['preview_size'],
					'layout'	=>	'horizontal',
					'choices' => array(
						'medium'	=>	__("Medium",'acf'),
						'large'		=>	__("Large",'acf'),
						'full'		=>	__("Full",'acf')                                                
					)
				));
				
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?> <?=  $field['key']?>_option">
			<td class="label">
				<label><?php _e("Crop type",'acf'); ?></label>
                                <p class="description">
                                    <strong>Fixed:</strong> the aspect ratio is locked to the specified dimensions.<br/><br/>
                                    <strong>Variable height/width:</strong> One dimension is fixed to the specified size, while the other can be selected between the min and max specified. Leave blank for no min/max.<br/><br/>
                                    <strong>Free:</strong> The user can freely crop the image as desired.
                                </p>
			</td>
			<td class="crop_type_select">
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][crop_type]',
					'value'	=>	$field['crop_type'],
					'layout'	=>	'horizontal',
					'choices' => array(
						'fixed'	=>	__("Fixed",'acf'),
						'var_height'		=>	__("Variable height",'acf'),
						'var_width'		=>	__("Variable width",'acf'),
                                                'free'		=>	__("Free",'acf')
					)
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?> <?=  $field['key']?>_option">
			<td class="label">
				<label><?php _e("Dimensions",'acf'); ?></label>                                
			</td>
			<td>
                            <table class="crop_dimensions">
                                <tr class="crop_dimension width">
                                    <td>Width:</td>
                                    <td>
				<?php								
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][width]',
					'value'	=>	$field['width']					
				));                                                                
				?>
                                    </td>
                                </tr>
                                <tr class="crop_dimension height">
                                <td>Height:</td>
                                    <td>
				<?php								
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][height]',
					'value'	=>	$field['height']					
				));                                                                
				?>
                                    </td>
                                </tr>
                                
                                <tr class="crop_dimension min-width">
                                    <td>Min-Width:</td>
                                    <td>
				<?php								
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][min_width]',
					'value'	=>	$field['min_width']					
				));                                                                
				?>
                                    </td>
                                </tr>
                                
                                <tr class="crop_dimension max-width">
                                    <td>Max-Width:</td>
                                    <td>
				<?php								
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][max_width]',
					'value'	=>	$field['max_width']					
				));                                                                
				?>
                                    </td>
                                </tr>
                                
                                <tr class="crop_dimension min-height">
                                    <td>Min-height:</td>
                                    <td>
				<?php								
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][min_height]',
					'value'	=>	$field['min_height']					
				));                                                                
				?>
                                    </td>
                                </tr>
                                
                                <tr class="crop_dimension max-height">
                                    <td>Max-height:</td>
                                    <td>
				<?php								
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][max_height]',
					'value'	=>	$field['max_height']					
				));                                                                
				?>
                                    </td>
                                </tr>
                                
                            </table>
                <script type="text/javascript">
                    jQuery(function($){
                        $(document).ready(function(){
                            var prefix = '.<?= $field['key'] ?>_option ';                            
                            $(prefix + '.crop_type_select input[type=radio]').click(function(){                                
                               switch($(this).val()){
                                   case 'fixed':
                                       $(prefix + '.crop_dimension').hide();
                                       $(prefix + '.crop_dimension.width').show();
                                       $(prefix + '.crop_dimension.height').show();
                                       break;
                                   case 'var_height':
                                       $(prefix + '.crop_dimension').hide();
                                       $(prefix + '.crop_dimension.width').show();
                                       $(prefix + '.crop_dimension.min-height').show();
                                       $(prefix + '.crop_dimension.max-height').show();
                                       break;
                                   case 'var_width':
                                       $(prefix + '.crop_dimension').hide();
                                       $(prefix + '.crop_dimension.height').show();
                                       $(prefix + '.crop_dimension.min-width').show();
                                       $(prefix + '.crop_dimension.max-width').show();
                                       break;
                                  default:
                                      $(prefix + '.crop_dimension').hide();                                      
                               }
                           }); 
                           $(prefix + '.crop_dimension').hide();                           
                           $(prefix + '.crop_type_select input[checked=checked]').click();
                        });                       
                    });
                    
                </script>
			</td>
		</tr>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	pre_save_field
	*	- this function is called when saving your acf object. Here you can manipulate the
	*	field object and it's options before it gets saved to the database.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function pre_save_field($field)
	{
		// do stuff with field (mostly format options data)
                /*
		$field['max_width'] = intval($field['max_width']);
                $field['min_width'] = intval($field['min_width']);
                $field['max_height'] = intval($field['max_height']);
                $field['min_height'] = intval($field['min_height']);
                $field['width'] = intval($field['width']);
                $field['height'] = intval($field['height']);*/
		return parent::pre_save_field($field);
	}
        
        
        
        /*---------------------------------------------------------------------------------------------
	 * popup_head - STYLES MEDIA THICKBOX
	 *
	 * @author Elliot Condon
	 * @since 1.1.4
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function popup_head()
	{
	
		// defults
		$access = false;
		$tab = "type";
		$preview_size = "large";
		
		
		// GET
		if(isset($_GET["acf_type"]) && $_GET['acf_type'] == 'image')
		{
			$access = true;
			if( isset($_GET['tab']) ) $tab = $_GET['tab'];
			if( isset($_GET['acf_preview_size']) ) $preview_size = $_GET['acf_preview_size'];
			
			if( isset($_POST["attachments"]) )
			{
				echo '<div class="updated"><p>' . __("Media attachment updated.") . '</p></div>';
			}
			
		}
		
		
		if( $access )
		{
			
?><style type="text/css">
	#media-upload-header #sidemenu li#tab-type_url,
	#media-upload-header #sidemenu li#tab-gallery,
	#media-items .media-item a.toggle,
	#media-items .media-item tr.image-size,
	#media-items .media-item tr.align,
	#media-items .media-item tr.url,
	#media-items .media-item .slidetoggle {
		display: none !important;
	}
	
	#media-items .media-item {
		min-height: 68px;
	}
	
	#media-items .media-item .acf-checkbox {
		float: left;
		margin: 28px 10px 0;
	}
	
	#media-items .media-item .pinkynail {
		max-width: 64px;
		max-height: 64px;
		display: block !important;
	}
	
	#media-items .media-item .filename.new {
		min-height: 0;
		padding: 20px 10px 10px 10px;
		line-height: 15px;
	}
	
	#media-items .media-item .title {
		line-height: 14px;
	}
	
	#media-items .media-item .acf-select {
		float: right;
		margin: 22px 12px 0 10px;
	}
	
	#media-upload .ml-submit {
		display: none !important;
	}

	#media-upload .acf-submit {
		margin: 1em 0;
		padding: 1em 0;
		position: relative;
		overflow: hidden;
		display: none; /* default is hidden */
		clear: both;
	}
	
	#media-upload .acf-submit a {
		float: left;
		margin: 0 10px 0 0;
	}

</style>
<script type="text/javascript">
(function($){	
		
	/*
	*  Select Image
	*
	*  @created : 28/03/2012
	*/
	
	$('#media-items .media-item a.acf-select').live('click', function(){
		
		var id = $(this).attr('href');
		
		var data = {
			action: 'acf_get_preview_image',
			id: id,
			preview_size : "<?php echo $preview_size; ?>"
		};
	
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.ajax({
			url: ajaxurl,
			data : data,
			cache: false,
			dataType: "json",
			success: function( json ) {
		    	

				// validate
				if(!json)
				{
					return false;
				}
				
				
				// get item
				var item = json[0],
					div = self.parent.acf_div;
				
				
				// update acf_div
				div.find('input.value').val( item.id ).trigger('change');
	 			div.find('img').attr( 'src', item.url );
	 			div.addClass('active');
	 	
	 	
	 			// validation
	 			div.closest('.field').removeClass('error');
	 			
	 			
	 			// reset acf_div and return false
	 			self.parent.acf_div = null;
	 			self.parent.tb_remove();
	 	
	 	
			}
		});
		
		return false;
		
	});
	
	
	$('#acf-add-selected').live('click', function(){ 
		 
		// check total 
		var total = $('#media-items .media-item .acf-checkbox:checked').length;
		if(total == 0) 
		{ 
			alert("<?php _e("No images selected",'acf'); ?>"); 
			return false; 
		} 
		
		
		// generate id's
		var attachment_ids = [];
		$('#media-items .media-item .acf-checkbox:checked').each(function(){
			attachment_ids.push( $(this).val() );
		});
		
		
		// creae json data
		var data = {
			action: 'acf_get_preview_image',
			id: attachment_ids.join(','),
			preview_size : "<?php echo $preview_size; ?>"
		};
		
		
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.getJSON(ajaxurl, data, function( json ) {
			
			// validate
			if(!json)
			{
				return false;
			}
			
			$.each(json, function(i ,item){
			
				// update acf_div
				self.parent.acf_div.find('input.value').val( item.id ).trigger('change');                                                                                                
                                
	 			self.parent.acf_div.find('img').attr('src', item.url ); 
	 			self.parent.acf_div.addClass('active'); 
	 	 
	 	 
	 			// validation 
	 			self.parent.acf_div.closest('.field').removeClass('error'); 
	
	 			 
	 			if((i+1) < total) 
	 			{ 
	 				// add row 
	 				self.parent.acf_div.closest('.repeater').find('.add-row-end').trigger('click'); 
	 			 
	 				// set acf_div to new row image 
	 				self.parent.acf_div = self.parent.acf_div.closest('.repeater').find('> table > tbody > tr.row:last .acf-image-uploader'); 
	 			} 
	 			else 
	 			{ 
	 				// reset acf_div and return false 
					self.parent.acf_div = null; 
					self.parent.tb_remove(); 
	 			} 
				
    		});

			
		
		});
		
		return false;
		 
	}); 
	
	
	// edit toggle
	$('#media-items .media-item a.acf-toggle-edit').live('click', function(){
		
		if( $(this).hasClass('active') )
		{
			$(this).removeClass('active');
			$(this).closest('.media-item').find('.slidetoggle').attr('style', 'display: none !important');
			return false;
		}
		else
		{
			$(this).addClass('active');
			$(this).closest('.media-item').find('.slidetoggle').attr('style', 'display: table !important');
			return false;
		}
		
	});
	
	
	// set a interval function to add buttons to media items
	function acf_add_buttons()
	{
		// vars
		var is_sub_field = (self.parent.acf_div && self.parent.acf_div.closest('.repeater').length > 0) ? true : false;
		
		
		// add submit after media items (on for sub fields)
		if($('.acf-submit').length == 0 && is_sub_field)
		{
			$('#media-items').after('<div class="acf-submit"><a id="acf-add-selected" class="button"><?php _e("Add selected Images",'acf'); ?></a></div>');
		}
		
		
		// add buttons to media items
		$('#media-items .media-item:not(.acf-active)').each(function(){
			
			// show the add all button
			$('.acf-submit').show();
			
			// needs attachment ID
			if($(this).children('input[id*="type-of-"]').length == 0){ return false; }
			
			// only once!
			$(this).addClass('acf-active');
			
			// find id
			var id = $(this).children('input[id*="type-of-"]').attr('id').replace('type-of-', '');
			
			// if inside repeater, add checkbox
			if(is_sub_field)
			{
				$(this).prepend('<input type="checkbox" class="acf-checkbox" value="' + id + '" <?php if($tab == "type"){echo 'checked="checked"';} ?> />');
			}
			
			// Add edit button
			$(this).find('.filename.new').append('<br /><a href="#" class="acf-toggle-edit">Edit</a>');
			
			// Add select button
			$(this).find('.filename.new').before('<a href="' + id + '" class="button acf-select"><?php _e("Select Image",'acf'); ?></a>');
			
			// add save changes button
			$(this).find('tr.submit input.button').hide().before('<input type="submit" value="<?php _e("Update Image",'acf'); ?>" class="button savebutton" />');

			
		});
	}
	<?php
	
	// run the acf_add_buttons ever 500ms when on the image upload tab
	if($tab == 'type'): ?>
	var acf_t = setInterval(function(){
		acf_add_buttons();
	}, 500);
	<?php endif; ?>
	
	
	// add acf input filters to allow for tab navigation
	$(document).ready(function(){
		
		setTimeout(function(){
			acf_add_buttons();
		}, 1);
		
		
		$('form#filter').each(function(){
			
			$(this).append('<input type="hidden" name="acf_preview_size" value="<?php echo $preview_size; ?>" />');
			$(this).append('<input type="hidden" name="acf_type" value="image" />');
						
		});
		
		$('form#image-form, form#library-form').each(function(){
			
			var action = $(this).attr('action');
			action += "&acf_type=image&acf_preview_size=<?php echo $preview_size; ?>";
			$(this).attr('action', action);
			
		});
		
		
		<?php
	
		// add support for media tags
		
		if($tab == 'mediatags'): ?>
		$('#media-items .mediatag-item-count a').each(function(){
			
			var href = $(this).attr('href');
			href += "&acf_type=image&acf_preview_size=<?php echo $preview_size; ?>";
			$(this).attr('href', href);
			
		});
		<?php endif; ?>
	});
				
})(jQuery);
</script><?php

		}
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*	- this function is called on edit screens to produce the html for this field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		// vars
		$class = "";
		$file_src = "";
		$preview_size = 'large';		                
                
		// get image url
		if($field['value'] != '' && is_numeric($field['value']))
		{
			$file_src = wp_get_attachment_image_src($field['value'], $preview_size);
			$file_src = $file_src[0];
			
			if($file_src)
			{
				$class = "active";
			}
		}
                
                $escapedName = $field['name'];
                
                $cropOptions = array(
                    'show' => 'true',
                    'handles' => 'true',
                    'x1' => 0,
                    'y1' => 0,                     
                );
               
                switch($field['crop_type']){
                    case 'fixed':
                        $cropOptions['aspectRatio'] = '"' . $field['width'] . ':' . $field['height'] . '"';
                        $cropOptions['minWidth'] = $field['width'];
                        $cropOptions['minHeight'] = $field['height'];
                        $cropOptions['x2'] = $field['width'];
                        $cropOptions['y2'] = $field['height'];
                            
                        //$cropOptions['aspectRatio'] = '"16:9"';
                        //$cropOptions['persistent'] = 'true';
                        /*$cropOptions['width'] = $field['width'];
                        $cropOptions['height'] = $field['height'];*/
                        break;
                    case 'var_width':
                        $cropOptions['minHeight'] = $field['height'];
                        $cropOptions['maxHeight'] = $field['height'];
                        $cropOptions['minWidth'] = $field['min_width'];
                        $cropOptions['maxWidth'] = $field['max_width'];
                        $cropOptions['x2'] = $field['max_width'];
                        $cropOptions['y2'] = $field['height'];
                        break;
                    case 'var_height':
                        $cropOptions['minWidth'] = $field['width'];
                        $cropOptions['maxWidth'] = $field['width'];
                        $cropOptions['minHeight'] = $field['min_height'];
                        $cropOptions['maxHeight'] = $field['max_height'];
                        $cropOptions['x2'] = $field['width'];
                        $cropOptions['y2'] = $field['max_height'];
                        break;
                }
		?>
<script>
    // TESTING
    jQuery(function($){                       
       $('.field-<?= $this->name ?> .<?= $field['key'] ?>_cropButton').click(function(){            
           // create json data    '    
           console.log(this);
            var $image = $(this).parents('.acf-image-uploader').find('.has-image > img');
            var $field = $(this).parents('.acf-image-uploader');            
            if($(this).hasClass('active')){
               $field.find('.cropData[name="<?= $field['key'] ?>_crop"]').val('false');
               $($image).imgAreaSelect({
                   disabled:true,
                   hide:true
               });
               $(this).val('Crop');
            }
            else{                                                            
                $field.find('.cropData[name="<?= $field['key'] ?>_crop"]').val('true');
                $(this).val('Cancel crop');
                var data = {
                        action: 'acf_crop_get_image_size',
                        image_id: $(this).parents('.acf-image-uploader').find('input.value').val()                    
                };

                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                $.getJSON(ajaxurl, data, function( json ) {
                    console.log(json);
                    $($image).imgAreaSelect({
                        <?php 
                        foreach(array_keys($cropOptions) as $key){
                            echo $key . ': ' . $cropOptions[$key] . ',
                                ';
                        }
                        ?>
                        imageWidth : json.width,
                        imageHeight : json.height,
                        enabled : true,
                        onSelectEnd: function(img, selection){
                            $field.find('.cropData[name="<?= $field['key'] ?>_crop_x1"]').val(selection.x1);
                            $field.find('.cropData[name="<?= $field['key'] ?>_crop_y1"]').val(selection.y1);
                            $field.find('.cropData[name="<?= $field['key'] ?>_crop_w"]').val(selection.width);
                            $field.find('.cropData[name="<?= $field['key'] ?>_crop_h"]').val(selection.height);                    
                            }
                        });
                });
            }
            $(this).toggleClass('active');
            
        }); 
    });
</script>


<div class="acf-image-uploader clearfix <?php echo $class; ?>" data-preview_size="<?php echo $preview_size; ?>">
	<input class="value" type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" />  
        <input class="cropData" type="hidden" name="<?= $field['key'] ?>_crop" value="false"/>
        <input class="cropData" type="hidden" name="<?= $field['key'] ?>_crop_x1"/>
        <input class="cropData" type="hidden" name="<?= $field['key'] ?>_crop_y1"/>
        <input class="cropData" type="hidden" name="<?= $field['key'] ?>_crop_w"/>
        <input class="cropData" type="hidden" name="<?= $field['key'] ?>_crop_h"/>
	<div class="has-image">
		<div class="hover">
			<ul class="bl">
				<li><a class="remove-image ir" href="#"><?php _e("Remove",'acf'); ?></a></li>
				<li><a class="edit-image ir" href="#"><?php _e("Edit",'acf'); ?></a></li>
			</ul>
		</div>
               
		<img src="<?php echo $file_src; ?>" alt=""/>
                <p>
                    <input type="button" class="button <?= $field['key'] ?>_cropButton" value="Crop"></button>
                </p>
	</div>
	<div class="no-image">
		<p><?php _e('No image selected','acf'); ?> <input type="button" class="button add-image" value="<?php _e('Add Image','acf'); ?>" />
	</div>
</div>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*	- this function is called in the admin_head of the edit screen where your field
	*	is created. Use this function to create css and javascript to assist your 
	*	create_field() function.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{

	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*	- this function is called in the admin_print_scripts / admin_print_styles where 
	*	your field is created. Use this function to register css and javascript to assist 
	*	your create_field() function.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_scripts()
	{
            wp_enqueue_script('img_area_select', get_bloginfo('stylesheet_directory') . '/fields/lib/imgareaselect/jquery.imgareaselect.min.js', 'jquery');
	}
	
	function admin_print_styles()
	{
            wp_enqueue_style('img_area_select', get_bloginfo('stylesheet_directory') . '/fields/lib/imgareaselect/imgareaselect-default.css', 'jquery');	
	}

	
	/*--------------------------------------------------------------------------------------
	*
	*	update_value
	*	- this function is called when saving a post object that your field is assigned to.
	*	the function will pass through the 3 parameters for you to use.
	*
	*	@params
	*	- $post_id (int) - usefull if you need to save extra data or manipulate the current
	*	post object
	*	- $field (array) - usefull if you need to manipulate the $value based on a field option
	*	- $value (mixed) - the new value of your field.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_value($post_id, $field, $value)
	{        
            require_once WP_CONTENT_DIR . "/../wp-admin/includes/file.php";
            require_once WP_CONTENT_DIR . "/../wp-admin/includes/image.php";                
            if($_POST[$field['key'] . '_crop'] == 'true'){                
                $filename = wp_crop_image($value, $_POST[$field['key'] . '_crop_x1'], $_POST[$field['key'] . '_crop_y1'], $_POST[$field['key'] . '_crop_w'], $_POST[$field['key'] . '_crop_h'], $field['width'], $field['height']);   

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

                // CHANGE VALUE TO NEW ATTACHMENT
                $value = $attach_id;
            }
                
            // save value
            parent::update_value($post_id, $field, $value);
	
	
        }
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*	- called from the edit page to get the value of your field. This function is useful
	*	if your field needs to collect extra data for your create_field() function.
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value($post_id, $field)
	{
		// get value
		$value = parent::get_value($post_id, $field);
		
		// format value
		
		// return value
		return $value;		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*	- called from your template file when using the API functions (get_field, etc). 
	*	This function is useful if your field needs to format the returned value
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		// vars
		$format = isset($field['save_format']) ? $field['save_format'] : 'url';
		$value = parent::get_value($post_id, $field);
		
		
		// validate
		if( !$value )
		{
                    return false;
		}
		
		
		// format
		if($format == 'url')
		{
			$value = wp_get_attachment_url($value);
		}
		elseif($format == 'object')
		{
			$attachment = get_post( $value );
			
			
			// validate
			if( !$attachment )
			{
				return false;	
			}
			
			
			// create array to hold value data
			$value = array(
				'id' => $attachment->ID,
				'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content,
				'url' => wp_get_attachment_url( $attachment->ID ),
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
					$value['sizes'][$image_size] = $src[0];
				}
				// foreach( $image_sizes as $image_size )
			}
			// if( $image_sizes )
			
		}
		
		return $value;

	}
	
}

?>