jQuery(document).ready(function($){
	// onclick for 'Style' radio buttons
	$('input[name="ro3_options[style]"]').on('click', function(){
		// clear out all preview items
		$('#ro3-preview div[id^=preview]').css('display', 'none');
		// get new value
		var option = this.value;
		// activate the preview for selected value
		$('#ro3-preview div#preview-' + option).css('display', 'block');
	});
	
	// onclick for post type radio buttons
	$('input.ro3-post-type-select').on('click', function(){
		// make sure we got a valid selection
		bValid = true;
		// get post type clicked on
		var sType = $(this).val();
		// get section
		var nSection = $(this).attr('data-section');
		$.post(
			ajaxurl,
			{
				post_type: sType,
				action: 'ro3_get_posts_for_type',
				section: nSection,
			},
			function(data){
				$('#post-select-'+nSection)
				.css('display', 'block')
				.html(data);
			}
		);
		
		// uncheck if we didn't get a post
		if(!bValid) $(this).prop('checked', false);
	});
	
	// onclick for post select dropdown
	$(document).on('change', '.post-select select', function(){
		// newly selected value
		var id = $(this).attr('value');
		if('' == id) return;
		var nSection = $(this).attr('data-section');
		if('' == nSection) return;
		// fill in this block with select post's data
		$.post(
			ajaxurl,
			{
				action: 'get_block_data_for_post',
				post_id: id,
			},
			function(data){
				post = JSON.parse(data);
				// title
				if('' != post.post_title){
					$('input#title'+nSection).attr('value', post.post_title);
				}
				// thumbnail
				if('' != post.thumb){
					$('input#image'+nSection).attr('value', post.thumb);
					$('div#image'+nSection+'-thumb-preview img').attr('src', post.thumb);					
				}
				else{
					$('input#image'+nSection).attr('value', '');					
					$('div#image'+nSection+'-thumb-preview img').attr('src', '');					
				}
				// description
				$('textarea#description'+nSection).html(post.post_excerpt);
				// link
				$('input#link'+nSection).attr('value', post.url);
			}
		);

	});
});