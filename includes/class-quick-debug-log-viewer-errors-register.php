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
     * Detects critical errors in the debug log.
     * If $get_all is true, it returns all relevant errors found;
     * otherwise, it returns only the most recent one.
     *
     * @since    1.0.0
     * @param    string         $debug_log_file_path    Path to the debug.log file.
     * @param    callable|null  $callback               Optional callback to handle the detected error(s).
     * @param    bool           $get_all                Whether to retrieve all errors or just the last one.
     * @return   array|null     An array of error data or null if none found.
     */
    public function detect_errors($debug_log_file_path, $get_all = false, $callback = null) {
        if (!file_exists($debug_log_file_path) || !is_readable($debug_log_file_path)) {
            return null;
        }

        $errors = [];
        try {
            $file = new SplFileObject($debug_log_file_path, 'r');
        } catch (RuntimeException $e) {
            return null;
        }

        if ($get_all) {
            // Read the entire file line by line.
            while (!$file->eof()) {
                $line = $file->fgets();
                if ($this->is_relevant_error_line($line)) {
                    $parsed = $this->parse_error_line($line);
                    if ($parsed) {
                        $errors[] = $parsed;
                    }
                }
            }
            if ($callback) {
                try {
                    $callback($errors);
                } catch (Exception $e) {
                }
            }
            return $errors;
        } else {
            // Retrieve only the most recent relevant error.
            $file->seek(PHP_INT_MAX);
            while ($file->key() > 0) {
                $file->seek($file->key() - 1);
                $line = $file->current();
                if ($this->is_relevant_error_line($line)) {
                    $parsed = $this->parse_error_line($line);
                    if ($callback) {
                        try {
                            $callback($parsed);
                        } catch (Exception $e) {
                        }
                    }
                    return $parsed;
                }
            }
        }

        return $get_all ? [] : null;
    }

    /**
     * Determines if a given log line contains a relevant error.
     *
     * @param    string    $line    The log line to check.
     * @return   bool               True if it contains a critical error, false otherwise.
     */
    private function is_relevant_error_line($line) {
        return stripos($line, 'PHP Fatal error') !== false || 
        stripos($line, 'Uncaught') !== false || 
        stripos($line, 'Parse error') !== false;
    }

    /**
     * Parses a log line into a structured error array.
     *
     * @param    string     $line    The log line.
     * @return   array|null          Parsed error data or null if not matched.
     */
    private function parse_error_line($line) {
        if (preg_match('/(.+): (.+) in (.+) on line (\\d+)/', $line, $matches)) {
            return [
                'type'      => trim($matches[1]),
                'message'   => trim($matches[2]),
                'file'      => trim($matches[3]),
                'line'      => (int) trim($matches[4]),
                'timestamp' => time(),
            ];
        }
        return null;
    }

    /**
     * Updates the custom error log (JSON) by adding a new error.
     *
     * @since    1.0.0
     * @param    array   $error                 The error data to log.
     * @param    string  $custom_log_file_path  Path to the custom JSON log file.
     * @return   void
     */
    public function update_custom_error_log($error, $custom_log_file_path) {
        if (!is_array($error)) {
            return;
        }

        $error_data = [
            'type'      => $error['type'],
            'message'   => $error['message'],
            'file'      => $error['file'],
            'line'      => $error['line'],
            'timestamp' => $error['timestamp'],
        ];

        if (file_exists($custom_log_file_path)) {
            $errors = json_decode(file_get_contents($custom_log_file_path), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors = []; // Reset if JSON is corrupted.
            }
        } else {
            $errors = [];
        }

        $errors[] = $error_data;

        $allowed_dir = realpath(WP_CONTENT_DIR);
        $real_path = realpath(dirname($custom_log_file_path));
        if (strpos($real_path, $allowed_dir) !== 0) {
            return;
        }
        file_put_contents($custom_log_file_path, json_encode($errors, JSON_PRETTY_PRINT));
    }

    /**
     * Checks if two error entries are identical.
     *
     * @since    1.0.0
     * @param    array   $last_error    The last recorded error.
     * @param    array   $error         The current error to compare.
     * @return   bool                   True if both errors are identical, false otherwise.
     */
    public function check_if_same_errors($last_error, $error) {
        if (!is_array($last_error) || !is_array($error)) {
            return false;
        }
        return $last_error['timestamp'] === $error['timestamp'] &&
               $last_error['type']      === $error['type'] &&
               $last_error['message']   === $error['message'] &&
               $last_error['file']      === $error['file'] &&
               $last_error['line']      === $error['line'];
    }
}
