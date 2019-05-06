jQuery(document).ready(function($) {

	function start() {
		$('.cldflr-data-update').hide();
		$('.cldflr-data-loading').show();
	}

	$(document).on('click', '.cldflr-data-update', function() {
		start();
		return false;
	});

	if ($('.cldflr-data').data('auto')) {
		start();
	}

});