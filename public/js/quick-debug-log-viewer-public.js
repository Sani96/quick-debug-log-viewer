(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
	$( function() {
		function filterLogs(type) {
			const allLogs = document.querySelectorAll('#quick-debug-log-content .alert');
			if (type !== 'all') {
				isSearching = true;
				$('#load-more-errors').hide();
			} else {
				isSearching = false;
				$('#load-more-errors').show();
			}
			allLogs.forEach(log => {
				log.style.display = (type === 'all' || log.classList.contains(type)) ? 'block' : 'none';
			});
			document.querySelectorAll('.quick-debug-log-viewer-filter button').forEach(btn => btn.classList.remove('active'));
			document.querySelector(`.quick-debug-log-viewer-filter button[onclick*="${type}"]`)?.classList.add('active');
			updateArrowVisibility();
		}
		window.filterLogs = filterLogs;

		function updateArrowVisibility() {
			const container = document.querySelector('.quick-debug-log-container');
			const upBtn = document.getElementById('scroll-up-btn');
			const downBtn = document.getElementById('scroll-down-btn');
			if (!container || !upBtn || !downBtn) return;
			const scrollTop = container.scrollTop;
			const scrollHeight = container.scrollHeight;
			const offsetHeight = container.offsetHeight;
			upBtn.style.display = scrollTop > 10 ? 'block' : 'none';
			downBtn.style.display = (scrollTop + offsetHeight) < (scrollHeight - 10) ? 'block' : 'none';
		}

		$('.quick-debug-log-container').on('scroll', updateArrowVisibility);
		updateArrowVisibility();

		// Scroll up
		$('#scroll-up-btn').on('click', function () {
			const container = $('.quick-debug-log-container');
			container.animate({ scrollTop: 0 }, 400);
		});

		// Scroll down
		$('#scroll-down-btn').on('click', function () {
			const container = $('.quick-debug-log-container');
			container.animate({ scrollTop: container[0].scrollHeight }, 400);
		});

		// Live search
		let searchTimer;
		let isSearching = false;
		$('#quick-debug-log-viewer-log-search').on('input', function () {
			clearTimeout(searchTimer);
			const keyword = $(this).val().trim();
			const nonce = $(this).data('nonce');
			searchTimer = setTimeout(() => {
				if (keyword.length > 0) {
					isSearching = true;
					$('#load-more-errors').hide();
				} else {
					isSearching = false;
					$('#load-more-errors').show();
				}
				$.post(quick_debug_log_viewer_public_ajax.ajax_url, {
					action: 'quick_debug_log_viewer_public_search_debug_log',
					keyword,
					nonce
				}, function (response) {
					const $logContent = $('#quick-debug-log-content');
					$logContent.empty();
					if (response.success && response.data.length > 0) {
						response.data.forEach(item => {
							const div = $('<div></div>').addClass('alert').addClass(item.class).text(item.text);
							$logContent.append(div);
						});
					} else {
						$logContent.html('<p style="padding:1rem;">No results found.</p>');
					}
					updateArrowVisibility();
				});
			}, 300);
		});

		// Load More
		let offset = 0;
		$('#load-more-errors').on('click', function () {
			if (isSearching) return;
			const btn = $(this);
			btn.prop('disabled', true).text('Loading...');
			$.post(quick_debug_log_viewer_public_ajax.ajax_url, {
				action: 'quick_debug_log_viewer_public_load_more_debug_blocks',
				offset,
				nonce: btn.data('nonce')
			}, function (response) {
				if (response.success && response.data.length > 0) {
					response.data.forEach(block => {
						$('#quick-debug-log-content').append(`<div class="alert ${block.class}">${block.text}</div>`);
					});
					offset += response.data.length;
					btn.prop('disabled', false).text('Load More');
					updateArrowVisibility();
				} else {
					btn.text('No more logs').prop('disabled', true);
				}
			});
		});

		// Open modal on FAB click
		$('#quick-debug-log-viewer-fab').on('click', function () {
			$('#quick-debug-log-viewer-modal').fadeIn();
			offset = 0;
			$('#quick-debug-log-content').empty();
			$('#load-more-errors').trigger('click');
		});

		// Close modal button
		$('#quick-debug-log-viewer-modal-close').on('click', function () {
			$('#quick-debug-log-viewer-modal').fadeOut();
		});

		// Close modal with Escape key
		$(document).on('keydown', function (e) {
			if (e.key === 'Escape') {
				$('#quick-debug-log-viewer-modal').fadeOut();
			}
		});

		// Click outside to close
		$('#quick-debug-log-viewer-modal').on('click', function (e) {
			if (e.target === this) {
				$(this).fadeOut();
			}
		});

		$('#quick-debug-log-viewer-clear-log').on('click', function(e) {
			e.preventDefault();
			if (!confirm('Are you sure you want to clear the debug.log file?')) return;
			$.post(quick_debug_log_viewer_public_ajax.ajax_url, {
				action: 'quick_debug_log_viewer_public_clear_debug_log',
				nonce: $(this).data('nonce')
			}, function(response) {
				alert(response.success ? 'Log cleared!' : 'Error: ' + response.data);
				$('#quick-debug-log-content').html('<p>No errors found.</p>');
				offset = 0;
				$('#load-more-errors').prop('disabled', false).text('Load More');
			});
		});

		$('#quick-debug-log-viewer-download-log').on('click', function(e) {
			e.preventDefault();
			$.post(quick_debug_log_viewer_public_ajax.ajax_url, {
				action: 'quick_debug_log_viewer_public_download_debug_log',
				nonce: $(this).data('nonce')
			}, function(response) {
				if (response.success) {
					const blob = new Blob([response.data.content], { type: 'text/plain' });
					const link = document.createElement('a');
					link.href = URL.createObjectURL(blob);
					link.download = response.data.filename;
					link.click();
				} else {
					alert('Error: ' + response.data);
				}
				button.prop('disabled', false).text('Clear debug.log');
			});
		});
	});

})( jQuery );
