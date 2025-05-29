<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpsani.store
 * @since      1.0.0
 *
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <div class="quick-debug-log-viewer-search-container">
        <input type="text" id="quick-debug-log-viewer-log-search" placeholder="Search log…"  data-nonce="<?php echo esc_attr(wp_create_nonce('search_debug_log_nonce')); ?>">
        <span class="dashicons dashicons-search"></span>
    </div>
    <div class="quick-debug-log-viewer-admin-display-errors">
        <div class="quick-debug-log-viewer-filter">
            <strong>Filter logs:</strong>
            <button type="button" class="button" onclick="filterLogs('all')">All</button>
            <button type="button" class="button" onclick="filterLogs('error-fatal')">Fatal</button>
            <button type="button" class="button" onclick="filterLogs('error-warning')">Warning</button>
            <button type="button" class="button" onclick="filterLogs('error-notice')">Notice</button>
        </div>

        <div class="quick-debug-log-container" style="position:relative;max-height: 600px; overflow-y: scroll; background: #1e1e1e; color: #f5f5f5; padding: 10px; border: 1px solid #ccc; font-family: monospace; font-size: 12px; line-height: 1.4; margin-top: 20px;">
            <button id="scroll-up-btn" class="scroll-btn dashicons dashicons-arrow-up-alt2" title="Scroll to Top"></button>
            <?php
            if ( isset( $errors ) && $errors ) {
                $lines = preg_split('/\r\n|\r|\n/', trim($errors));
                foreach ( $lines as $line ) {
                    $css_class = '';
                    if ( stripos($line, 'Fatal') !== false ) {
                        $css_class = 'error-fatal';
                    } elseif ( stripos($line, 'Warning') !== false ) {
                        $css_class = 'error-warning';
                    } elseif ( stripos($line, 'Notice') !== false ) {
                        $css_class = 'error-notice';
                    }
                    echo '<div class="' . esc_attr($css_class) . '">' . esc_html($line) . '</div>';
                }
            } else {
                esc_html_e('No errors found.', 'quick-debug-log-viewer');
            }
            ?>
            <button id="scroll-down-btn" class="scroll-btn dashicons dashicons-arrow-down-alt2" title="Scroll to Bottom"></button>
        </div>

    </div>

    <div class="quick-debug-log-viewer-actions">
        <form method="post" style="margin-bottom: 10px;">
            <?php wp_nonce_field('clear_debug_log_action', 'clear_debug_log_nonce'); ?>
            <input type="hidden" name="clear_debug_log" value="1">
            <button type="submit" class="button button-secondary">Clear debug.log</button>
        </form>
        <a href="<?php echo esc_url(admin_url('admin-post.php?action=download_debug_log')); ?>" class="button button-primary" style="margin-bottom: 10px;">Download debug.log</a>
    </div>
</div>