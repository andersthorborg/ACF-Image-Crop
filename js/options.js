jQuery(function($){
	acf.add_action('append', function(){
		$('.acf-field-object-image-crop .target-size-select').each(function() {
			toggleCustomDimensions(this);
		});
		$('.acf-field-object-image-crop .save-in-media-library-select input').each(function() {
			//console.log(this);
			toggleSaveFormats(this);
		});
	});

	$(document).on('change', '.acf-field-object-image-crop .target-size-select', function(e) {
		toggleCustomDimensions(this);
	});

	// $(document).on('change', '.acf-field-object-image-crop .crop-type-select', function(e) {
	// 	$(this).parents('.acf-field-object-image-crop').find('.dimensions-wrap .dimensions-description').addClass('hidden');
	// 	$(this).parents('.acf-field-object-image-crop').find('.dimensions-wrap .dimensions-description[data-type=' + $(this).val() + ']').removeClass('hidden');
	// });

	$(document).on('click', '.acf-field-object-image-crop .save-in-media-library-select input', function(e) {
		toggleSaveFormats(this);
		// var saveToMedia = $(this).val() == 'yes';
		// var $returnValueField = $(this).parents('.acf-field-object-image-crop').find('.return-value-select');
		// if(! saveToMedia){
		// 	$returnValueField.find('input[value=id], input[value=object]').attr('disabled', true).parents('label').addClass('disabled');
		// 	$returnValueField.find('input[value=url]').attr('checked', true);
		// }
		// else{
		// 	$returnValueField.find('input').removeAttr('disabled').parents('label').removeClass('disabled');
		// }

		// $(this).parents('.acf-field-object-image-crop').find('.dimensions-wrap .dimensions-description').addClass('hidden');
		// $(this).parents('.acf-field-object-image-crop').find('.dimensions-wrap .dimensions-description[data-type=' + $(this).val() + ']').removeClass('hidden');

	});

	function toggleSaveFormats(saveToMediaSelect){
		if($(saveToMediaSelect).is(':checked')){
			var saveToMedia = $(saveToMediaSelect).val() == 'yes';
			var $returnValueField = $(saveToMediaSelect).parents('.acf-field-object-image-crop').find('.return-value-select');
			if(! saveToMedia){
				$returnValueField.find('input[value=id]').attr('disabled', true).parents('label').addClass('disabled');
				if($returnValueField.find('input[value=id]').is(':checked')){
					$returnValueField.find('input[value=url]').attr('checked', true);
				}
			}
			else{
				$returnValueField.find('input').removeAttr('disabled').parents('label').removeClass('disabled');
			}
		}
	}

	function toggleCustomDimensions(targetSizeSelect){
		if($(targetSizeSelect).val() == 'custom'){
			$(targetSizeSelect).parents('.acf-field-object-image-crop').first().find('.custom-target-dimension').each(function(){
				$(this).parents('tr.acf-field').first().removeClass('hidden');
			});
		}
		else{
			$(targetSizeSelect).parents('.acf-field-object-image-crop').first().find('.custom-target-dimension').each(function(){
				$(this).parents('tr.acf-field').first().addClass('hidden');
			});
		}
	}

	$('.acf-field-object-image-crop .target-size-select').each(function() {
		toggleCustomDimensions(this);
	});
	$('.acf-field-object-image-crop .save-in-media-library-select input').each(function() {
		toggleSaveFormats(this);
	});

});