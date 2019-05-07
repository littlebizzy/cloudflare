jQuery(document).ready(function($) {

	function start(auto) {

		$('.cldflr-data-update').hide();
		$('.cldflr-data-loading').show();

		var data = {
			'action' : 'cldflr_dns_records_update',
			'nonce'  : $('.cldflr-data').attr('data-nonce'),
			'auto'	 : auto ? 1 : 0
		}

		$.post(ajaxurl, data, function(e) {

			if ('undefined' == typeof e.status) {
				$('.cldflr-data').hide();
				$('.cldflr-error').show().html('Unknown error');

			} else if ('error' == e.status) {
				$('.cldflr-data').hide();
				$('.cldflr-error').show().html(e.reason);

			} else if ('ok' == e.status) {
				$('.cldflr-data').html(e.html);
				setTimeout(function() {
					$('.cldflr-data-done').hide();
					$('.cldflr-data-update').show();
				}, 60 * 1000);
			}

		}).fail(function() {
			$('.cldflr-error').show().html('Server communication error. Please wait a few minutes and try again.');
			setTimeout(function() {
				$('.cldflr-data-update').show();
			}, 15 * 1000);

		}).always(function() {
			$('.cldflr-data-loading').hide();
		});
	}

	$(document).on('click', '.cldflr-data-update', function() {
		start();
		return false;
	});

	if ($('.cldflr-data').data('auto')) {
		start(true);
	}

});