
$(document).ready(function() {

	$('.mycharts-toggle-btn').click(function() {
		$(this).closest('.mycharts').find('.mycharts-body').slideToggle(500);
	});

});