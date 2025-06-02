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
        <input type="text" id="quick-debug-log-viewer-log-search" placeholder="Search log…" data-nonce="<?php echo esc_attr(wp_create_nonce('quick_debug_log_viewer_admin_search_debug_log_nonce')); ?>">
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

        <div class="quick-debug-log-container">
            <button id="scroll-up-btn" class="scroll-btn dashicons dashicons-arrow-up-alt2" title="Scroll to Top"></button>

            <div id="quick-debug-log-content" class="log-content">
                <?php
                if ( isset( $blocks ) && is_array( $blocks ) && !empty( $blocks ) ) {
                    foreach ( $blocks as $block ) {
                        $css_class = isset($block['class']) ? $block['class'] : 'error-secondary';
                        echo '<div class="alert ' . esc_attr($css_class) . '">';
                        echo esc_html($block['text']);
                        echo '</div>';
                    }
                } else {
                    echo '<p>' . esc_html__('No errors found.', 'quick-debug-log-viewer') . '</p>';
                }
                ?>
            </div>

            <button id="scroll-down-btn" class="scroll-btn dashicons dashicons-arrow-down-alt2" title="Scroll to Bottom"></button>
        </div>


        <div style="margin-top: 1rem; text-align: center;">
            <button data-nonce="<?php echo esc_attr(wp_create_nonce('quick_debug_log_viewer_admin_load_more_debug_log_nonce')); ?>" id="load-more-errors" class="button button-secondary">Load More</button>
        </div>

    </div>

    <div class="quick-debug-log-viewer-actions">
        <button id="quick-debug-log-viewer-admin-clear-log"
                class="button button-secondary"
                data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_debug_log_viewer_admin_clear_log_nonce' ) ); ?>">
            Clear debug.log
        </button>

        <button id="quick-debug-log-viewer-admin-download-log"
                class="button button-primary"
                data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_debug_log_viewer_admin_download_debug_log_nonce' ) ); ?>">
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