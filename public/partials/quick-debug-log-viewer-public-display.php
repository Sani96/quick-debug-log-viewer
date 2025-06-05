<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://wpsani.store
 * @since      1.0.0
 *
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/public/partials
 */
?>

<div id="quick-debug-log-viewer-fab" title="View debug.log">     
    <span class="dashicons dashicons-editor-code"></span>
    <span class="screen-reader-text">Open debug log viewer</span>
    <span class="fab-options-indicator">⋮</span>
    <div id="quick-debug-fab-menu" class="fab-menu" style="display: none;">
        <button id="reset-fab-position" class="fab-menu-item">↺ Reset position</button>
    </div>
</div>
<div id="quick-debug-log-viewer-modal">
    <div id="quick-debug-log-viewer-modal-content">
        <button id="quick-debug-log-viewer-modal-close">×</button>
        <h2>Quick debug.log Viewer</h2>

        <div class="quick-debug-log-viewer-search-container">
            <input type="text" id="quick-debug-log-viewer-log-search" placeholder="Search log…"  data-nonce="<?php echo esc_attr(wp_create_nonce('quick_debug_log_viewer_public_search_debug_log_nonce')); ?>">
            <span class="dashicons dashicons-search"></span>
        </div>

        <div class="quick-debug-log-viewer-filter">
            <strong>Filter logs:</strong>
            <button type="button" class="button" onclick="filterLogs('all')">All</button>
            <button type="button" class="button" onclick="filterLogs('error-fatal')">Fatal</button>
            <button type="button" class="button" onclick="filterLogs('error-warning')">Warning</button>
            <button type="button" class="button" onclick="filterLogs('error-notice')">Notice</button>
        </div>

        <div class="quick-debug-log-container">
            <button id="scroll-up-btn" class="scroll-btn dashicons dashicons-arrow-up-alt2" title="Scroll to Top"></button>
            <div id="quick-debug-log-content" class="log-content"></div>        
            <button id="scroll-down-btn" class="scroll-btn dashicons dashicons-arrow-down-alt2" title="Scroll to Bottom"></button>
        </div>

        <div class="quick-debug-log-viewer-load-more-container">
            <button id="load-more-errors"
                    class="button button-secondary"
                    data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_debug_log_viewer_public_load_more_debug_log_nonce' ) ); ?>">
                Load More
            </button>
        </div>

        <div class="quick-debug-log-viewer-actions">
            <button id="quick-debug-log-viewer-clear-log"
                    class="button button-secondary"
                    data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_debug_log_viewer_public_clear_log_nonce' ) ); ?>">
                Clear debug.log
            </button>

            <button id="quick-debug-log-viewer-download-log"
                    class="button button-primary"
                    data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_debug_log_viewer_public_download_log_nonce' ) ); ?>">
                Download debug.log
            </button>
        </div>

        <div class="wpsani-footer-bar">
            <span>
                <?php esc_html_e( 'Discover more on', 'quick-debug-log-viewer' ); ?>
                <a href="<?php echo esc_url( 'https://wpsani.store/?utm_source=debug_log_viewer&utm_medium=wpsani_footer&utm_campaign=plugin_referral' ); ?>" target="_blank">
                    wpsani.store
                </a>
            </span>
            <a href="<?php echo esc_url( 'https://wpsani.store/?utm_source=debug_log_viewer&utm_medium=wpsani_footer&utm_campaign=plugin_referral' ); ?>" class="footer-cta" target="_blank">
                ❤️ <?php esc_html_e( 'Explore more plugins made with love →', 'quick-debug-log-viewer' ); ?>
            </a>
        </div>

    </div>
</div>