<?php
/**
 * Quick_Debug_Log_Viewer_Errors_Register class
 *
 * Handles the detection and registration of PHP critical errors
 * from the WordPress debug.log file.
 *
 * @link       https://wpsani.store
 * @since      1.0.0
 *
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/includes
 */

class Quick_Debug_Log_Viewer_Errors_Register {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The plugin identifier.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current plugin version.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name    The name of the plugin.
     * @param    string    $version        The version of the plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Retrieves the full raw content of the debug log file.
     *
     * @since    1.0.0
     * @param    string  $debug_log_file_path  Path to the debug.log file.
     * @return   string|null                   The full file content or null if not readable.
     */
    public function get_raw_log_content($debug_log_file_path) {
        if (!file_exists($debug_log_file_path) || !is_readable($debug_log_file_path)) {
            return null;
        }
        return trim(file_get_contents($debug_log_file_path));
    }

    /**
     * Parses the debug log file and returns an array of critical errors.
     *
     * @since    1.0.0
     * @param    string  $debug_log_file_path  Path to the debug.log file.
     * @return   array|null                    An array of error data or null if no errors found.
     */
    public function parse_debug_log_blocks($file_path) {
        if (!file_exists($file_path)) {
            return [];
        }

        $lines = file($file_path, FILE_IGNORE_NEW_LINES);
        $blocks = [];
        $current_block = '';

        foreach ($lines as $line) {
            if (preg_match('/(PHP )?(Fatal error|Warning|Notice|Deprecated|Parse error|Uncaught)/i', $line)) {
                if (!empty(trim($current_block))) {
                    $blocks[] = trim($current_block);
                }
                $current_block = $line . "\n";
            } else {
                $current_block .= $line . "\n";
            }
        }

        if (!empty(trim($current_block))) {
            $blocks[] = trim($current_block);
        }

        return $blocks;
    }
}