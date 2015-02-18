jQuery(document).ready(function($){
	$('input[name="ro3_options[style]"]').on('click', function(){
		// clear out all preview items
		$('#ro3-preview div[id^=preview]').css('display', 'none');
		// get new value
		var option = this.value;
		// activate the preview for selected value
		$('#ro3-preview div#preview-' + option).css('display', 'block');
	});
});