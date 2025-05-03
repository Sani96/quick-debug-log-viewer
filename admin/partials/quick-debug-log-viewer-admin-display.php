<?php

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
    <div id="quick-debug-log-viewer-admin-display">
        <div class="quick-debug-log-viewer-admin-display-content">
            <p><?php esc_html_e( 'You can view your debug.log here.', 'quick-debug-log-viewer' ); ?></p>
        </div>
    </div>
    <div class="quick-debug-log-viewer-admin-display-errors">
        <form method="post" style="margin-bottom: 10px;">
            <?php wp_nonce_field('clear_debug_log_action', 'clear_debug_log_nonce'); ?>
            <input type="hidden" name="clear_debug_log" value="1">
            <button type="submit" class="button button-secondary">Clear debug.log</button>
        </form>
        <a href="<?php echo esc_url(admin_url('admin-post.php?action=download_debug_log')); ?>" class="button button-primary" style="margin-bottom: 10px;">Download debug.log</a>

        <div class="quick-debug-log-container" style="max-height: 600px; overflow-y: scroll; background: #1e1e1e; color: #f5f5f5; padding: 10px; border: 1px solid #ccc; font-family: monospace; font-size: 12px; line-height: 1.4; margin-top: 20px;">
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
        </div>

    </div>
</div>