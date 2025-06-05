=== Quick debug.log Viewer ===
Contributors: sani060913  
Tags: debug, error log, admin, troubleshooting, logging  
Requires at least: 6.0  
Tested up to: 6.8  
Stable tag: 1.2.2
Requires PHP: 7.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Easily view and manage your WordPress debug.log file directly from the admin area — no FTP access required.

== Description ==

**Quick debug.log Viewer** lets you quickly inspect your site's debug.log file without leaving the WordPress dashboard. Now with a **floating action button (FAB)** and modal for frontend viewing, searching, and filtering!
The FAB is now draggable and its position is remembered — with a right-click option to reset.

- Instantly view, scroll, **search**, and filter your `debug.log` contents  
- 🔍 Search and browse logs even from the frontend with a floating action button (FAB) and modal  
- Filter logs by type: Fatal, Warning, Notice — or show all  
- Clear the log with a single click (now via AJAX)  
- Download the log for backups or support  
- Load large logs in chunks with the **Load More** button  
- Secure HTML output with escaping to prevent session theft  
- Stack traces are shown in single blocks for better readability  
- Works even if `WP_DEBUG` is off — as long as the file exists  

Ideal for developers, site managers, and anyone needing to troubleshoot WordPress issues fast.

👉 Learn more and download from: [https://wpsani.store/downloads/quick-debug-log-viewer-free/](https://wpsani.store/downloads/quick-debug-log-viewer-free/)

== Installation ==

1. Upload the plugin to `/wp-content/plugins/quick-debug-log-viewer`, or install it directly via the WordPress plugin screen.  
2. Activate the plugin through the 'Plugins' screen in WordPress.  
3. Go to **Tools → Quick debug.log Viewer** to see your site's debug log.

== Frequently Asked Questions ==

= Where is the debug.log file? =  
It’s typically located in your `wp-content` directory at `wp-content/debug.log`.

= Do I need to enable anything for this to work? =  
The plugin works best when `WP_DEBUG` and `WP_DEBUG_LOG` are enabled in `wp-config.php`, but it can still read the file if it exists and has proper read permissions.

= Can I clear the log from the dashboard? =  
Yes — just click the “Clear Log” button to safely empty the log file.

== Screenshots ==

1. View and filter your debug.log directly in the admin panel.
2. FAB and modal for frontend viewing, searching, and filtering.

== Changelog ==

= 1.2.2 =
* The frontend **floating action button (FAB)** is now **draggable** — place it wherever you want on screen.
* The FAB's position is **saved and restored** between visits.
* Added a **right-click menu** on the FAB to **reset its position** to default.
* UI polish: added an indicator (`⋮`) to show users that the FAB has options.
* Prevented the modal from opening if the FAB is dragged instead of clicked.

= 1.2.1 =
* Updated plugin description to reflect new frontend features and recent improvements.

= 1.2.0 =
* Introduced a **floating action button (FAB)** and modal on the frontend, allowing you to view, search, and filter the debug.log even while browsing your site.
* Refactored the internal logic into a dedicated `Log_Reader` class to centralize the parsing and block detection across frontend and backend.
* Added a `Formatter` class to dynamically assign CSS classes based on error types (fatal, warning, notice, etc).
* Replaced the legacy log-clear form with an **AJAX-powered log clearing**, improving user experience and UI consistency.
* Disabled “Load More” while performing an active search, to prevent inconsistent results.
* Removed deprecated `Errors_Register` class.
* Polished the admin and frontend UI for improved readability and consistency.

= 1.0.4 =
* Improved regex for better parsing of multi-line log entries and stack traces  
* Added escaping of log output with `esc_html()` for enhanced security  
* Implemented “Load More” button to progressively fetch large logs in 30-block chunks  
* Optimized AJAX search to work on the last 300 blocks only  
* Improved stability when viewing very large debug.log files  
* Refined UI to support dynamic block loading and consistent scroll buttons

= 1.0.3 =
* Added AJAX-powered search to filter debug.log entries by keyword in real-time  
* Moved scroll buttons inside the log viewer for better UX on long logs  

= 1.0.2 =
* Added sticky scroll-to-top and scroll-to-bottom buttons inside the log viewer  
* Added filter controls to view only Fatal errors, Warnings, Notices, or all logs  
* Styled UI controls to match the WPSani brand (compact, modern, accessible)  
* Improved usability and readability for long debug logs  

= 1.0.1 =
* Updated menu label for clarity  
* Fixed typos in readme.txt  
* Added admin screenshot  

= 1.0.0 =
* Initial release.  
* View, scroll, and search the debug log inside the dashboard.  
* Clear the log with a single click.  
* Download the log for backups or support.

== Upgrade Notice ==
= 1.2.2 =
You can now drag the frontend FAB and reset its position with a right-click — more control, same simplicity.