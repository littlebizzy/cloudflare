jQuery(document).ready(function($) {

	function start() {

		$('.cldflr-data-update').hide();
		$('.cldflr-data-loading').show();

		var data = {
			'action' : 'cldflr_dns_records_update',
			'nonce'  : $('.cldflr-data').attr('data-nonce')
		}

		$.post(ajaxurl, data, function(e) {

			if ('undefined' == typeof e.status) {
				alert('Unknown error');

			} else if ('error' == e.status) {
				alert(e.reason);

			} else if ('ok' == e.status) {
				$('.cldflr-data').html(e.html);
				setTimeout(function() {
					$('.cldflr-data-done').hide();
					$('.cldflr-data-update').show();
				}, 60 * 1000);
			}

		}).fail(function() {
			alert('Server communication error.' + "\n" + 'Please try again.');

		}).always(function() {
			$('.cldflr-data-loading').hide();
		});
	}

	$(document).on('click', '.cldflr-data-update', function() {
		start();
		return false;
	});

	if ($('.cldflr-data').data('auto')) {
		start();
	}

});