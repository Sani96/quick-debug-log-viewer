(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {
		/**
		 * Filter logs based on type
		 * 
		 * @since 1.0.2 
		 * @param {*} type
		 * @returns {void} 
		 */
		function filterLogs(type) {
			const allLogs = document.querySelectorAll('.quick-debug-log-container > div');
			allLogs.forEach(log => {
				if (type === 'all') {
					log.style.display = 'block';
				} else {
					log.style.display = log.classList.contains(type) ? 'block' : 'none';
				}
			});
		}	
		window.filterLogs = filterLogs;
	
		const container = document.querySelector('.quick-debug-log-container');
		const upBtn = document.getElementById('scroll-up-btn');
		const downBtn = document.getElementById('scroll-down-btn');

		function updateArrowVisibility() {
			if (!container) return;

			const scrollTop = container.scrollTop;
			const scrollHeight = container.scrollHeight;
			const offsetHeight = container.offsetHeight;

			upBtn.style.display = scrollTop > 10 ? 'block' : 'none';
			downBtn.style.display = (scrollTop + offsetHeight) < (scrollHeight - 10) ? 'block' : 'none';
		}

		container.addEventListener('scroll', updateArrowVisibility);
		updateArrowVisibility();

		upBtn.addEventListener('click', () => {
			container.scrollTo({ top: 0, behavior: 'smooth' });
		});

		downBtn.addEventListener('click', () => {
			container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
		});

		// Search functionality
		const $search = $('#quick-debug-log-viewer-log-search');		
		const nonce = $search.data('nonce');
		let timer;

		$search.on('input', function () {
			clearTimeout(timer);
			const keyword = $search.val();
			timer = setTimeout(function () {
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'search_debug_log',
						keyword: keyword,
						nonce: nonce
					},
					success: function (response) {
						$('.quick-debug-log-container').html(response);
					}
				});
			}, 300);
		});
	});

})( jQuery );
