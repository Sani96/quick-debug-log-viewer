=== Quick debug.log Viewer ===
Contributors: sani060913  
Tags: debug, error log, admin, troubleshooting, logging  
Requires at least: 6.0  
Tested up to: 6.8  
Stable tag: 1.0.3  
Requires PHP: 7.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Easily view and manage your WordPress debug.log file directly from the admin area — no FTP access required.

== Description ==

**Quick debug.log Viewer** lets you quickly inspect your site's debug.log file without leaving the WordPress dashboard.

- Instantly view, scroll, **search**, and filter your `debug.log` contents  
- Filter logs by type: Fatal, Warning, Notice — or show all  
- Clear the log with a single click  
- Download the log for backups or support  
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

== Changelog ==

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

= 1.0.3 =
New AJAX-powered search lets you instantly find specific entries in the log. Improved layout of scroll buttons.
