(function($){
	
	
	/*
	*  acf/setup_fields
	*
	*  This event is triggered when ACF adds any new elements to the DOM. 
	*
	*  @type	function
	*  @since	1.0.0
	*  @date	01/01/12
	*
	*  @param	event		e: an event object. This can be ignored
	*  @param	Element		postbox: An element which contains the new HTML
	*
	*  @return	N/A
	*/
	
	$(document).on('acf/setup_fields', function(e, postbox){				
		$(postbox).find('.field_type-image_crop').each(function(){
			var $field = $(this), $options = $field.find('.acf-image-uploader');			
			$field.find('.acf-image-value').on('change', function(){
				var originalImage = $(this).val();
				if($(this).val()){
					$field.removeClass('invalid');
					$field.find('.init-crop-button').removeAttr('disabled');
					$field.find('.acf-image-value').data('original-image', originalImage);
					$field.find('.acf-image-value').data('cropped-image', originalImage);
					$field.find('.acf-image-value').data('cropped', false);				
					$.post(ajaxurl, {action: 'acf_image_crop_get_image_size', image_id: originalImage}, function(data, textStatus, xhr) {
						if($field.find('img.crop-image').length == 0){
							$field.find('.crop-action').append($('<img class="crop-image" src="#"/>'));
						}
						$field.find('img.crop-image').attr('src', data['url']);						
						$field.find('img.crop-image').data('width', data['width']);						
						$field.find('img.crop-image').data('height', data['height']);						
						var warnings = [];
						var valid = true;
						if($options.data('width') && data['width'] < $options.data('width')){
							warnings.push('Width should be at least: ' + $options.data('width') + 'px (Selected image width: ' + data['width'] + 'px)');
							valid = false;
						}
						if($options.data('height') && data['height'] < $options.data('height')){
							warnings.push('Height should be at least: ' + $options.data('height') + 'px (Selected image height: ' + data['height'] + 'px)');
							valid = false;
						}
						if(!valid){
							$field.addClass('invalid');
							$field.find('.init-crop-button').attr('disabled', 'disabled');
							alert('Warning: The selected image is smaller than the required size:\n' + warnings.join('\n'));
						}
						else{
							if($options.data('force-crop')){
								initCrop($field);
							}	
						}
						
					}, 'json');
					updateFieldValue($field);
				}
				else{
					//Do nothing
				}
				
			})			
			$field.find('.init-crop-button').click(function(e){				
				e.preventDefault();
				initCrop($field);
			});
			$field.find('.perform-crop-button').click(function(e){
				e.preventDefault();
				performCrop($field);
			});
			$field.find('.cancel-crop-button').click(function(e){
				e.preventDefault();
				cancelCrop($field);
			});
		});					
	
	});

	function initCrop($field){
		var $options = $field.find('.acf-image-uploader');
		var options = {
			handles: true,
			onSelectEnd: function (img, selection) { 
				updateThumbnail($field, img, selection);		        
				updateCropData($field, img, selection);		        
		    },
		    imageWidth:$options.find('.crop-stage img.crop-image').data('width'),
		    imageHeight:$options.find('.crop-stage img.crop-image').data('height'),
		    x1: 0,
		    y1: 0
		};
		if($options.data('crop-type') == 'hard'){
			options.aspectRatio = $options.data('width') + ':' + $options.data('height');
			options.minWidth = $options.data('width');
			options.minHeight = $options.data('height');
			options.x2 = $options.data('width');
			options.y2 = $options.data('height'); 					
		}
		else if($options.data('crop-type') == 'min'){
			if($options.data('width')){
				options.minWidth = $options.data('width');	
				options.x2 = $options.data('width');
			}
			else{
				options.x2 = options.imageWidth;
			}
			if($options.data('height')){
				options.minHeight = $options.data('height');	
				options.y2 = $options.data('height');
			}
			else{
				options.y2 = options.imageHeight;
			}			
		}
		if(!$field.hasClass('invalid')){
			toggleCropView($field);	
			$field.find('.crop-stage img.crop-image').imgAreaSelect(options);
			updateCropData($field, $field.find('.crop-stage img.crop-image').get(0), {y1: options.y1, y2: options.y2, x1: options.x1, x2: options.x2});
			updateThumbnail($field, $field.find('.crop-stage img.crop-image').get(0), {y1: options.y1, y2: options.y2, x1: options.x1, x2: options.x2});
		}
	}

	function updateCropData($field, img, selection){
		var $options = $field.find('.acf-image-uploader');
		$options.data('x1', selection.x1);
        $options.data('x2', selection.x2);
        $options.data('y1', selection.y1);
        $options.data('y2', selection.y2);
	}

	function updateThumbnail($field, img, selection){
		var $options = $field.find('.acf-image-uploader');
		var div = $field.find('.crop-preview .preview');
        var targetWidth = $field.find('.crop-preview .preview').width();
        var factor = targetWidth / (selection.x2 - selection.x1);
        //image
        div.css('background-image', 'url(' + img.src + ')');
        //width
        div.css('width', (selection.x2 - selection.x1) * factor);				       				        
        //height                
        div.css('height', (selection.y2 - selection.y1) * factor);
        //x offset
        div.css('background-position-x', 0-(selection.x1 * factor));
        //y offset
        div.css('background-position-y', 0-(selection.y1 * factor));
        div.css('background-size', $options.find('.crop-stage img.crop-image').data('width') * factor + 'px' + ' ' + $options.find('.crop-stage img.crop-image').data('height') * factor + 'px');
	}

	function generateCropJSON(originalImage, croppedImage){
		var obj = {
			original_image: originalImage,
			cropped_image: croppedImage
		}
		return JSON.stringify(obj);
	}

	function performCrop($field){		
		if(!$field.find('.crop-stage').hasClass('loading')){
			$field.find('.crop-stage').addClass('loading');
			var $options = $field.find('.acf-image-uploader');
			var targetWidth = $options.data('width');
			var targetHeight = $options.data('height');
			if($options.data('crop-type') == 'min'){
				targetWidth = $options.data('x2') - $options.data('x1');
				targetHeight = $options.data('y2') - $options.data('y1');
			}
			var data = {
				action: 'acf_image_crop_perform_crop',
				id: $field.find('.acf-image-value').data('original-image'),
				x1: $options.data('x1'),
				x2: $options.data('x2'),
				y1: $options.data('y1'),
				y2: $options.data('y2'),
				target_width: targetWidth,
				target_height: targetHeight,
				preview_size: $options.data('preview_size')
			}					
			$.post(ajaxurl, data, function(data, textStatus, xhr) {
				$field.find('.acf-image-image').attr('src', data.url_preview);	
				$field.find('.acf-image-value').data('cropped-image', data.id);
				$field.find('.acf-image-value').data('cropped', true);	
				updateFieldValue($field);			
				$field.find('.crop-stage').removeClass('loading');			
				cancelCrop($field);
			}, 'json');
		}
	}

	function cancelCrop($field){
		toggleCropView($field);	
		$field.find('.crop-stage img.crop-image').imgAreaSelect({remove:true});
	}

	function toggleCropView($field){
		if($field.hasClass('cropping')){
			$('#acf-image-crop-overlay').remove();
		}
		else{
			$('body').append($('<div id="acf-image-crop-overlay"></div>'));	
		}
		$field.toggleClass('cropping');	

	}

	function updateFieldValue($field){
		var $input = $field.find('.acf-image-value');		
		$input.val(generateCropJSON($input.data('original-image'), $input.data('cropped-image')));
	}

	function getFullImageUrl(id, callback){
		$.post(ajaxurl, {images: []}, function(data, textStatus, xhr) {
		}, 'json');
	}

})(jQuery);
