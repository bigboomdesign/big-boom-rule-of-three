jQuery(document).ready(function($){
	// square dimensions on circle images on page load and window resize
	circleShadows($);
	$(window).resize(function(){
		circleShadows($);
	});
});
function circleShadows($){
	// loop through <a> containers for circle images
	var circles = $("a.circle");
	circles.each(function(){
		var width = $(this).css("width");
		// set dimensions of <a> to square
		$(this).css("height", width);	
		// set dimensions of <img> to 100% height and compensate width
		$(this).find("img")
			.css('height', '100%')
			.css('width', 'auto');		
	});
}