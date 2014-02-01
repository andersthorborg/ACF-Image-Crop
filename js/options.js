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
});