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
     * Searches the debug log blocks for a specific keyword.
     *
     * @since    1.0.4
     * @param    string  $file_path   Path to the debug.log file.
     * @param    string  $keyword     Keyword to search for in the log blocks.
     * @param    int     $max_blocks  Maximum number of blocks to return.
     * @return   array                An array of matching log blocks.
     */
    public function search_debug_log_blocks_streaming($file_path, $keyword = '', $max_blocks = 300) {
        if (!file_exists($file_path) || !is_readable($file_path)) {
            return [];
        }

        $keyword = strtolower($keyword);
        $blocks = [];
        $current_block = '';
        $file = new SplFileObject($file_path, 'r');

        while (!$file->eof()) {
            $line = $file->fgets();

            // Inizio di nuovo errore
            if (preg_match('/(PHP )?(Fatal error|Warning|Notice|Deprecated|Parse error|Uncaught)/i', $line)) {
                if (!empty(trim($current_block))) {
                    if (empty($keyword) || strpos(strtolower($current_block), $keyword) !== false) {
                        $blocks[] = trim($current_block);
                        if (count($blocks) >= $max_blocks) break;
                    }
                }
                $current_block = $line;
            } else {
                $current_block .= $line;
            }
        }

        // Ultimo blocco
        if (!empty(trim($current_block))) {
            if (empty($keyword) || strpos(strtolower($current_block), $keyword) !== false) {
                $blocks[] = trim($current_block);
            }
        }

        return $blocks;
    }

    /**
     * Parses the debug log file in a streaming manner, returning blocks of errors.
     * This method is optimized for large files and can handle up to a specified maximum number of blocks.
     * It reads the file line by line, detecting error blocks based on specific patterns.
     * 
     * @since 1.0.4
     * @access public
     * @param string $file_path The path to the debug log file.
     * @param int $max_blocks The maximum number of error blocks to return. Default is 300.
     * @return array An array of error blocks, each block containing the full error message.
     */
    public function parse_debug_log_blocks_streaming($file_path, $max_blocks = 300) {
        if (!file_exists($file_path) || !is_readable($file_path)) {
            return [];
        }

        $blocks = [];
        $current_block = '';
        $file = new SplFileObject($file_path, 'r');

        while (!$file->eof()) {
            $line = $file->fgets();

            if (preg_match('/(PHP )?(Fatal error|Warning|Notice|Deprecated|Parse error|Uncaught)/i', $line)) {
                if (!empty(trim($current_block))) {
                    $blocks[] = trim($current_block);
                    if (count($blocks) >= $max_blocks) break;
                }
                $current_block = $line;
            } else {
                $current_block .= $line;
            }
        }

        if (!empty(trim($current_block)) && count($blocks) < $max_blocks) {
            $blocks[] = trim($current_block);
        }

        return $blocks;
    }
}