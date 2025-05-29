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

	console.log('Quick Debug Log Viewer Admin JS Loaded');
	
	window.filterLogs = filterLogs;

})( jQuery );
