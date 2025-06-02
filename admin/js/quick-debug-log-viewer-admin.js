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
			const allLogs = document.querySelectorAll('#quick-debug-log-content .alert');

			// Update isSearching state and visibility of load more button
			if (type !== 'all') {
				isSearching = true;
				$('#load-more-errors').hide();
			} else {
				isSearching = false;
				$('#load-more-errors').show();
			}

			allLogs.forEach(log => {
				if (type === 'all') {
					log.style.display = 'block';
				} else {
					log.style.display = log.classList.contains(type) ? 'block' : 'none';
				}
			});

			// Highlight active filter button
			document.querySelectorAll('.quick-debug-log-viewer-filter button').forEach(btn => {
				btn.classList.remove('active');
			});
			document.querySelector(`.quick-debug-log-viewer-filter button[onclick*="${type}"]`)?.classList.add('active');

			updateArrowVisibility();
		}

		window.filterLogs = filterLogs;
	
		let container = document.querySelector('.quick-debug-log-container');

		function updateArrowVisibility() {
			const upBtn = document.getElementById('scroll-up-btn');
			const downBtn = document.getElementById('scroll-down-btn');
			if (!container || !upBtn || !downBtn) return;

			// Conta solo gli .alert effettivamente visibili
			const alerts = container.querySelectorAll('.alert');
			let visibleCount = 0;

			alerts.forEach(alert => {
				if (alert.offsetParent !== null) {
					visibleCount++;
				}
			});

			// Se non ci sono log visibili, nascondi entrambe le frecce
			if (visibleCount === 0) {
				upBtn.style.display = 'none';
				downBtn.style.display = 'none';
				return;
			}

			const scrollTop = container.scrollTop;
			const scrollHeight = container.scrollHeight;
			const offsetHeight = container.offsetHeight;

			upBtn.style.display = scrollTop > 10 ? 'block' : 'none';
			downBtn.style.display = (scrollTop + offsetHeight) < (scrollHeight - 10) ? 'block' : 'none';
		}

		// Initialize scroll buttons
		container.addEventListener('scroll', updateArrowVisibility);
		updateArrowVisibility();

		// Scroll buttons functionality
		document.getElementById('scroll-up-btn')?.addEventListener('click', () => {
			container.scrollTo({ top: 0, behavior: 'smooth' });
		});
		document.getElementById('scroll-down-btn')?.addEventListener('click', () => {
			container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
		});

		// Search functionality
		const $search = $('#quick-debug-log-viewer-log-search');
		const nonce = $search.data('nonce');
		let timer;
		let isSearching = false;

		$search.on('input', function () {
			clearTimeout(timer);
			const keyword = $search.val().trim();

			timer = setTimeout(function () {
				if (keyword.length > 0) {
					isSearching = true;
					$('#load-more-errors').hide();
				} else {
					isSearching = false;
					$('#load-more-errors').show();
				}

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'quick_debug_log_viewer_admin_search_debug_log',
						keyword: keyword,
						nonce: nonce
					},
					success: function (response) {
						if (response.success && Array.isArray(response.data)) {							
							const $logContent = $('#quick-debug-log-content');
							$logContent.empty();
							if (response.data.length === 0) {
								$logContent.html('<p>No errors found.</p>');
								return;
							}
							response.data.forEach(item => {
								const div = $('<div></div>')
									.addClass('alert')
									.addClass(item.class)
									.text(item.text);
								$logContent.append(div);
							});

							updateArrowVisibility();
						}
					}
				});
			}, 300);
		});

		// Load more functionality
		let offset = $('.quick-debug-log-container .alert').length;
		$('#load-more-errors').on('click', function () {
			if (isSearching) return;

			const button = $(this);
			button.prop('disabled', true).text('Loading...');

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'quick_debug_log_viewer_admin_load_more_debug_blocks',
					offset: offset,
					nonce: $('#load-more-errors').data('nonce')
				},
				success: function (response) {
					if (response.success && response.data.length > 0) {
						response.data.forEach(block => {
							$('#quick-debug-log-content').append(`<div class="alert ${block.class}">${block.text}</div>`);
						});
						offset += response.data.length;
						button.prop('disabled', false).text('Load More');
					} else {
						button.text('No more logs').prop('disabled', true);
					}
				},
				error: function () {
					button.text('Error loading').prop('disabled', false);
				}
			});
		});

		
		$('#quick-debug-log-viewer-admin-clear-log').on('click', function(e) {
			e.preventDefault();
			if (!confirm('Are you sure you want to clear the debug.log file?')) return;
			$.post(quick_debug_log_viewer_admin_ajax.ajax_url, {
				action: 'quick_debug_log_viewer_admin_clear_log',
				nonce: $(this).data('nonce')
			}, function(response) {
				alert(response.success ? 'Log cleared!' : 'Error: ' + response.data);
				$('#quick-debug-log-content').html('<p style="padding:1rem;">No errors found.</p>');
				offset = 0;
				updateArrowVisibility();
				$('#load-more-errors').prop('disabled', false).text('Load More');
			});
		});

		$('#quick-debug-log-viewer-admin-download-log').on('click', function(e) {
			e.preventDefault();
			$.post(quick_debug_log_viewer_admin_ajax.ajax_url, {
				action: 'quick_debug_log_viewer_admin_download_log',
				nonce: $(this).data('nonce')
			}, function(response) {
				console.log('response', response);
				
				if (response.success) {
					const blob = new Blob([response.data], { type: 'text/plain' });
					const link = document.createElement('a');
					link.href = URL.createObjectURL(blob);
					link.download = 'debug.log';
					link.click();
				} else {
					alert('Error: ' + response.data);
				}
				button.prop('disabled', false).text('Clear debug.log');
			});
		});

	});

})( jQuery );
