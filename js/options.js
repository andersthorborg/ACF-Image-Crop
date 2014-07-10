jQuery(function($){
	acf.add_action('append', function(){
		$('.field_type-image_crop .target-size-select').each(function() {
			toggleCustomDimensions(this);
		});
		$('.field_type-image_crop .save-in-media-library-select input').each(function() {
			console.log(this);
			toggleSaveFormats(this);
		});
	});

	$(document).on('change', '.field_type-image_crop .target-size-select', function(e) {
		toggleCustomDimensions(this);
	});

	// $(document).on('change', '.field_type-image_crop .crop-type-select', function(e) {
	// 	$(this).parents('.field_type-image_crop').find('.dimensions-wrap .dimensions-description').addClass('hidden');
	// 	$(this).parents('.field_type-image_crop').find('.dimensions-wrap .dimensions-description[data-type=' + $(this).val() + ']').removeClass('hidden');
	// });

	$(document).on('click', '.field_type-image_crop .save-in-media-library-select input', function(e) {
		toggleSaveFormats(this);
		// var saveToMedia = $(this).val() == 'yes';
		// var $returnValueField = $(this).parents('.field_type-image_crop').find('.return-value-select');
		// if(! saveToMedia){
		// 	$returnValueField.find('input[value=id], input[value=object]').attr('disabled', true).parents('label').addClass('disabled');
		// 	$returnValueField.find('input[value=url]').attr('checked', true);
		// }
		// else{
		// 	$returnValueField.find('input').removeAttr('disabled').parents('label').removeClass('disabled');
		// }

		// $(this).parents('.field_type-image_crop').find('.dimensions-wrap .dimensions-description').addClass('hidden');
		// $(this).parents('.field_type-image_crop').find('.dimensions-wrap .dimensions-description[data-type=' + $(this).val() + ']').removeClass('hidden');

	});

	function toggleSaveFormats(saveToMediaSelect){
		if($(saveToMediaSelect).is(':checked')){
			var saveToMedia = $(saveToMediaSelect).val() == 'yes';
			var $returnValueField = $(saveToMediaSelect).parents('.field_type-image_crop').find('.return-value-select');
			if(! saveToMedia){
				$returnValueField.find('input[value=id], input[value=object]').attr('disabled', true).parents('label').addClass('disabled');
				$returnValueField.find('input[value=url]').attr('checked', true);
			}
			else{
				$returnValueField.find('input').removeAttr('disabled').parents('label').removeClass('disabled');
			}
		}
	}

	function toggleCustomDimensions(targetSizeSelect){
		if($(targetSizeSelect).val() == 'custom'){
			$(targetSizeSelect).parents('.field_type-image_crop').find('.custom-target-dimension').parents('tr.acf-field').removeClass('hidden');
		}
		else{
			$(targetSizeSelect).parents('.field_type-image_crop').find('.custom-target-dimension').parents('tr.acf-field').addClass('hidden');
		}
	}

	$('.field_type-image_crop .target-size-select').each(function() {
		toggleCustomDimensions(this);
	});
	$('.field_type-image_crop .save-in-media-library-select input').each(function() {
		toggleSaveFormats(this);
	});

});