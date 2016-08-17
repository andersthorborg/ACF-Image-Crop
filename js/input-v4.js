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
			var $field = $(this).find('.acf-image-crop');
			var $options = $field;
			// var $el = $field.find('.acf-image-crop');
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
			$field.on('click', '.acf-image-uploader .acf-button-edit', function( e ){
				e.preventDefault();
				e.stopPropagation();
				var id = $field.find('.acf-image-value').data('cropped-image');
				if(!$.isNumeric(id)){
					id = $field.find('.acf-image-value').data('original-image');;
				}
				acf.fields.image_crop.set({ $el : $(this).closest('.acf-image-uploader') }).edit(id);
			});
			// $field.find('[data-name=edit-button]').click(function(e){
			// 	e.preventDefault();
			// 	e.stopPropagation();
			// 	var id = $field.find('.acf-image-value').data('cropped-image');
			// 	if(!$.isNumeric(id)){
			// 		id = $field.find('.acf-image-value').data('original-image');;
			// 	}
			// 	//acf.fields.image_crop.edits(id);
			//});
		});

	});

	function initCrop($field){
		var $options = $field;
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
		// Center crop - disabled needs more testing
		// options.x1 = options.imageWidth/2 - (options.minWidth/2);
		// options.y1 = options.imageHeight/2 - (options.minHeight/2)
		// options.x2 = options.minWidth + options.x1;
		// options.y2 = options.minHeight + options.y1;
		//options.y1 = (options.imageHeight - options.minHeight) / 2;
		if(!$field.hasClass('invalid')){
			toggleCropView($field);
			$field.find('.crop-stage img.crop-image').imgAreaSelect(options);
			updateCropData($field, $field.find('.crop-stage img.crop-image').get(0), {y1: options.y1, y2: options.y2, x1: options.x1, x2: options.x2});
			updateThumbnail($field, $field.find('.crop-stage img.crop-image').get(0), {y1: options.y1, y2: options.y2, x1: options.x1, x2: options.x2});
		}
	}

	function updateCropData($field, img, selection){
		var $options = $field;
		$options.data('x1', selection.x1);
        $options.data('x2', selection.x2);
        $options.data('y1', selection.y1);
        $options.data('y2', selection.y2);
	}

	function updateThumbnail($field, img, selection){
		var $options = $field;
		var div = $field.find('.crop-preview .preview');
        var targetWidth = $field.find('.crop-preview .preview').width();
        var factor = targetWidth / (selection.x2 - selection.x1);
        //image
        div.css('background-image', 'url(' + img.src + ')');
        //width
        div.css('width', (selection.x2 - selection.x1) * factor);
        //height
        div.css('height', (selection.y2 - selection.y1) * factor);

        // Set offset - Fix by @christdg
        pos_x = 0-(selection.x1 * factor);
        pos_y = 0-(selection.y1 * factor);
        div.css('background-position', pos_x + 'px ' + pos_y + 'px');

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
			var $options = $field;
			var targetWidth = $options.data('width');
			var targetHeight = $options.data('height');
			var saveToMediaLibrary = $options.data('save-to-media-library');
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
				preview_size: $options.data('preview_size'),
				save_to_media_library: saveToMediaLibrary
			}
			$.post(ajaxurl, data, function(data, textStatus, xhr) {
				if(data.success){
					$field.find('.acf-image-image').attr('src', data.preview_url);
                    $field.find('.acf-image-value').data('cropped-image', data.value);
                    $field.find('.acf-image-value').data('cropped', true);
                    updateFieldValue($field);
                }
                else{
                    $field.append('<div class="error"><p>Sorry, an error occurred when trying to crop your image:</p>' + data.error_message);
                }
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


	// reference
	var _media = acf.media;


	acf.fields.image_crop = {

		$el : null,
		$input : null,

		o : {},

		set : function( o ){

			// merge in new option
			$.extend( this, o );


			// find input
			this.$input = this.$el.find('input[type="hidden"]');


			// get options
			this.o = acf.helpers.get_atts( this.$el );


			// multiple?
			this.o.multiple = this.$el.closest('.repeater').exists() ? true : false;


			// wp library query
			this.o.query = {
				type : 'image'
			};


			// library
			if( this.o.library == 'uploadedTo' )
			{
				this.o.query.uploadedTo = acf.o.post_id;
			}


			// return this for chaining
			return this;

		},
		init : function(){

			// is clone field?
			if( acf.helpers.is_clone_field(this.$input) )
			{
				return;
			}

		},
		add : function( image ){

			// this function must reference a global div variable due to the pre WP 3.5 uploader
			// vars
			var div = _media.div;


			// set atts
			div.find('.acf-image-image').attr( 'src', image.url );
			div.find('.acf-image-value').val( image.id ).trigger('change');


		 	// set div class
		 	div.addClass('active');


		 	// validation
			div.closest('.field').removeClass('error');

		},
		edit : function(id){



			// set global var
			_media.div = this.$el;


			// clear the frame
			_media.clear_frame();


			// create the media frame
			_media.frame = wp.media({
				title		:	acf.l10n.image.edit,
				multiple	:	false,
				button		:	{ text : acf.l10n.image.update }
			});


			// log events
			/*
			acf.media.frame.on('all', function(e){

				console.log( e );

			});
			*/

			// open
			_media.frame.on('open',function() {

				// set to browse
				if( _media.frame.content._mode != 'browse' )
				{
					_media.frame.content.mode('browse');
				}

				// add class
				_media.frame.$el.closest('.media-modal').addClass('acf-media-modal acf-expanded');


				// set selection
				var selection	=	_media.frame.state().get('selection'),
					attachment	=	wp.media.attachment( id );


				// to fetch or not to fetch
				if( $.isEmptyObject(attachment.changed) )
				{
					attachment.fetch();
				}

				selection.add( attachment );
			});


			// close
			_media.frame.on('close',function(){

				// remove class
				_media.frame.$el.closest('.media-modal').removeClass('acf-media-modal');

			});


			// Finally, open the modal
			acf.media.frame.open();

		},
		remove : function()
		{

			// set atts
		 	this.$el.find('.acf-image-image').attr( 'src', '' );
			this.$el.find('.acf-image-value').val( '' ).trigger('change');


			// remove class
			this.$el.removeClass('active');

		},
		popup : function()
		{
			// reference
			var t = this;

			// set global var
			_media.div = this.$el;


			// clear the frame
			_media.clear_frame();


			 // Create the media frame
			 _media.frame = wp.media({
				states : [
					new wp.media.controller.Library({
						library		:	wp.media.query( t.o.query ),
						multiple	:	t.o.multiple,
						title		:	acf.l10n.image.select,
						priority	:	20,
						filterable	:	'all'
					})
				]
			});


			/*acf.media.frame.on('all', function(e){

				console.log( e );

			});*/


			// customize model / view
			acf.media.frame.on('content:activate', function(){
				// vars
				var toolbar = null,
					filters = null;


				// populate above vars making sure to allow for failure
				try
				{
					toolbar = acf.media.frame.content.get().toolbar;
					filters = toolbar.get('filters');
				}
				catch(e)
				{
					// one of the objects was 'undefined'... perhaps the frame open is Upload Files
					//console.log( e );
				}


				// validate
				if( !filters )
				{
					return false;
				}


				// filter only images
				$.each( filters.filters, function( k, v ){

					v.props.type = 'image';

				});


				// no need for 'uploaded' filter
				if( t.o.library == 'uploadedTo' )
				{
					filters.$el.find('option[value="uploaded"]').remove();
					filters.$el.after('<span>' + acf.l10n.image.uploadedTo + '</span>')

					$.each( filters.filters, function( k, v ){

						v.props.uploadedTo = acf.o.post_id;

					});
				}


				// remove non image options from filter list
				filters.$el.find('option').each(function(){

					// vars
					var v = $(this).attr('value');


					// don't remove the 'uploadedTo' if the library option is 'all'
					if( v == 'uploaded' && t.o.library == 'all' )
					{
						return;
					}

					if( v.indexOf('image') === -1 )
					{
						$(this).remove();
					}

				});


				// set default filter
				filters.$el.val('image').trigger('change');

			});


			// When an image is selected, run a callback.
			acf.media.frame.on( 'select', function() {

				// get selected images
				selection = _media.frame.state().get('selection');

				if( selection )
				{
					var i = 0;

					selection.each(function(attachment){

				    	// counter
				    	i++;


				    	// select / add another image field?
				    	if( i > 1 )
						{
							// vars
							var $td			=	_media.div.closest('td'),
								$tr 		=	$td.closest('.row'),
								$repeater 	=	$tr.closest('.repeater'),
								key 		=	$td.attr('data-field_key'),
								selector	=	'td .acf-image-uploader:first';


							// key only exists for repeater v1.0.1 +
							if( key )
							{
								selector = 'td[data-field_key="' + key + '"] .acf-image-uploader';
							}


							// add row?
							if( ! $tr.next('.row').exists() )
							{
								$repeater.find('.add-row-end').trigger('click');

							}


							// update current div
							_media.div = $tr.next('.row').find( selector );

						}


				    	// vars
				    	var image = {
					    	id		:	attachment.id,
					    	url		:	attachment.attributes.url
				    	};

				    	// is preview size available?
				    	if( attachment.attributes.sizes && attachment.attributes.sizes[ t.o.preview_size ] )
				    	{
					    	image.url = attachment.attributes.sizes[ t.o.preview_size ].url;
				    	}

				    	// add image to field
				        acf.fields.image.add( image );


				    });
				    // selection.each(function(attachment){
				}
				// if( selection )

			});
			// acf.media.frame.on( 'select', function() {


			// Finally, open the modal
			acf.media.frame.open();


			return false;
		},

		// temporary gallery fix
		text : {
			title_add : "Select Image",
			title_edit : "Edit Image"
		}

	};

})(jQuery);
