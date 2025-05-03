=== Quick debug.log Viewer ===
Contributors: federicosanua
Tags: debug, error log, admin, troubleshooting, logging
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
View and manage the WordPress debug.log file directly from your admin area.

== Description ==
Easily view and manage your WordPress debug.log file directly from the admin area.
No need to access your server via FTP — visualize, scroll, and clear logs inside the dashboard.
Ideal for developers, site managers, or anyone needing to troubleshoot and view WordPress debug.log errors quickly.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/quick-debug-log-viewer` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to Debug Log Viewer to view your site's debug log.

== Frequently Asked Questions ==

= Where is the debug.log file? =
It’s located in your `wp-content` directory, typically at `wp-content/debug.log`.

= Do I need to enable anything for this to work? =
While the plugin is designed to work with the standard `debug.log` generated when `WP_DEBUG` and `WP_DEBUG_LOG` are set to `true` in `wp-config.php`, it can still read the `debug.log` file as long as it exists in the `wp-content` directory — even if those constants are disabled. 
Make sure the file exists and has read permissions.

= Can I clear the log from the dashboard? =
Yes! There's a button to safely clear the log without leaving your WordPress admin.

== Screenshots ==

== Changelog ==
= 1.0.0 =
* Initial release.
* View, scroll, and search the debug log inside the dashboard.
* Clear the log with a single click.
* Download the log for external backups.

== Upgrade Notice ==
= 1.0.0 =
Initial stable release.
