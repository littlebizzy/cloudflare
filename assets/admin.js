jQuery(document).ready(function($) {

	function start() {
		alert('start');
	}

	$('.cldflr-data-update').click(function() {
		start();
		return false;
	});

	if ($('.cldflr-data').data('auto')) {
		start();
	}

});