jQuery(function($){
	$(document).on('change', '.field_type-image_crop .target-size-select', function(e) {
		if($(this).val() == 'custom'){
			$(this).parents('.field_type-image_crop').find('.dimensions-wrap').removeClass('hidden');
		}
		else{
			$(this).parents('.field_type-image_crop').find('.dimensions-wrap').addClass('hidden');
		}

	});
	$(document).on('change', '.field_type-image_crop .crop-type-select', function(e) {
		$(this).parents('.field_type-image_crop').find('.dimensions-wrap .dimensions-description').addClass('hidden');
		$(this).parents('.field_type-image_crop').find('.dimensions-wrap .dimensions-description[data-type=' + $(this).val() + ']').removeClass('hidden');

	});
	$(document).on('click', '.field_type-image_crop .save-in-media-library-select input', function(e) {
		var saveToMedia = $(this).val() == 'yes';
		var $returnValueField = $(this).parents('.field_type-image_crop').find('.return-value-select');
		if(! saveToMedia){
			$returnValueField.find('input[value=id]').attr('disabled', true).parents('label').addClass('disabled');
			if($returnValueField.find('input[value=id]').is(':checked')){
				$returnValueField.find('input[value=url]').attr('checked', true);
			}
		}
		else{
			$returnValueField.find('input').removeAttr('disabled').parents('label').removeClass('disabled');
		}

		// $(this).parents('.field_type-image_crop').find('.dimensions-wrap .dimensions-description').addClass('hidden');
		// $(this).parents('.field_type-image_crop').find('.dimensions-wrap .dimensions-description[data-type=' + $(this).val() + ']').removeClass('hidden');

	});
	$('.field_type-image_crop .save-in-media-library-select input:checked').each(function(){
		$(this).click();
	});
});