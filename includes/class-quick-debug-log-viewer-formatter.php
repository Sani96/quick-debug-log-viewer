<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Formatter for the Quick Debug Log Viewer plugin.
 *
 * @link       https://wpsani.store
 * @since      1.2.0
 *
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/includes
 */

/**
 * Formatter for the Quick Debug Log Viewer plugin.
 * 
 * This class provides methods to classify and format log blocks
 * based on their content, such as errors, warnings, notices, and deprecations.
 * It also includes methods to format an array of log blocks.
 * 
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/includes
 * @author     WP Sani <support@wpsani.store>  
 */
class Quick_Debug_Log_Viewer_Formatter {

    /**
     * Classifies a log block based on its content.
     *
     * @since 1.2.0
     * @param string $block The log block to classify.
     * @return string The CSS class for the log block.
     */
	public static function classify_block($block) {
		$class = 'log-block';
		if (stripos($block, 'fatal error') !== false) {
			$class .= ' error-fatal';
		} elseif (stripos($block, 'warning') !== false) {
			$class .= ' error-warning';
		} elseif (stripos($block, 'notice') !== false) {
			$class .= ' error-notice';
		} elseif (stripos($block, 'deprecated') !== false) {
			$class .= ' error-deprecated';
		}
		return esc_attr($class);
	}

    /**
     * Formats a single log block.
     *
     * @since 1.2.0
     * @param string $block The log block to format.
     * @return array An associative array with the formatted text and class.
     */
	public static function format_block($block) {
		return [
			'text' => $block,
			'class' => self::classify_block($block)
		];
	}

    /**
     * Formats an array of log blocks.
     *
     * @since 1.2.0
     * @param array $blocks An array of log blocks to format.
     * @return array An array of formatted log blocks.
     */
	public static function format_blocks($blocks) {
    	return array_values( array_map([__CLASS__, 'format_block'], $blocks) );
	}
}
