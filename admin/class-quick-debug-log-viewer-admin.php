<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpsani.store
 * @since      1.0.0
 *
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/admin
 */

class Quick_Debug_Log_Viewer_Admin {

	private $plugin_name;
	private $version;
	private $errors_register;
	protected $loader;
	private $debug_log_file_path;
    private $clear_success = false; // Flag to show success notice

	/**
	 * Initialize the class and set its properties.
	 * 
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version           The version of the plugin.
	 * @param    object    $loader            The loader instance.
	 * @param    string    $debug_log_file_path The path to the debug log file.
	 */
	public function __construct($plugin_name, $version, $loader = null, $debug_log_file_path = null) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->loader = $loader;
		$this->debug_log_file_path = $debug_log_file_path ? $debug_log_file_path : WP_CONTENT_DIR . '/debug.log';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-quick-debug-log-viewer-errors-register.php';
		$this->errors_register = new Quick_Debug_Log_Viewer_Errors_Register($this->plugin_name, $this->version);

		$this->setup_admin_hooks();
	}

    /**
     * Setup all admin hooks cleanly.
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @return   void
     */
	public function setup_admin_hooks() {
		$this->loader->add_action('admin_menu', $this, 'add_admin_menu');
		$this->loader->add_action('admin_init', $this, 'handle_clear_log_request');
		$this->loader->add_action('admin_notices', $this, 'show_admin_notices');
		$this->loader->add_action('admin_post_download_debug_log', $this, 'admin_post_download_debug_log');
		$this->loader->add_action('wp_ajax_search_debug_log', $this, 'search_debug_log');
	}

	/**
	 * Search the debug log for a specific keyword.
	 * 
	 * @since    1.0.3
	 * @access   public
	 * @return   void
	 * 
	 */
	public function search_debug_log() {
		check_ajax_referer('search_debug_log_nonce', 'nonce');
		$keyword = isset($_POST['keyword']) ? sanitize_text_field(wp_unslash($_POST['keyword'])) : '';
		$log_path = WP_CONTENT_DIR . '/debug.log';

		if (!file_exists($log_path) || !is_readable($log_path)) {
			wp_send_json_error('debug.log not readable');
		}

		$lines = file($log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$output = '';

		foreach ($lines as $line) {
			if (stripos($line, $keyword) !== false || $keyword === '') {
				$css_class = '';
				if (stripos($line, 'Fatal') !== false) {
					$css_class = 'error-fatal';
				} elseif (stripos($line, 'Warning') !== false) {
					$css_class = 'error-warning';
				} elseif (stripos($line, 'Notice') !== false) {
					$css_class = 'error-notice';
				}
				$output .= '<div class="' . esc_attr($css_class) . '">' . esc_html($line) . '</div>';
			}
		}

		if ( $output ) {
			echo wp_kses_post( $output );
		} else {
			echo '<div>' . esc_html__( 'No matching results.', 'quick-debug-log-viewer' ) . '</div>';
		}		
		wp_die();
	}

    /**
     * Handle the POST request to clear the debug log.
     *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */

	public function handle_clear_log_request() {
		if (isset($_POST['clear_debug_log']) && check_admin_referer('clear_debug_log_action', 'clear_debug_log_nonce')) {
			if (!current_user_can('manage_options')) {
				return;
			}
			global $wp_filesystem;
			if (empty($wp_filesystem)) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$wp_filesystem->put_contents($this->debug_log_file_path, '', FS_CHMOD_FILE);
			$this->clear_success = true;
		}
	}

    /**
     * Show success message if log was cleared.
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @return   void
     */
	public function show_admin_notices() {
		if ($this->clear_success) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Debug log cleared successfully.', 'quick-debug-log-viewer') . '</p></div>';
		}
	}

	/**
	 * Add admin menu for the plugin.
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function add_admin_menu() {
		add_menu_page(
			'Quick debug.log Viewer',
			'Quick debug.log Viewer',
			'manage_options',
			'quick-debug-log-viewer',
			[$this, 'display_admin_page'],
			'dashicons-warning',
			80
		);
	}

	/**
	 * Display the admin page.
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function display_admin_page() {
		$errors = $this->errors_register->get_raw_log_content($this->debug_log_file_path);
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/quick-debug-log-viewer-admin-display.php';
	}

	/**
	 * Enqueue styles for the admin area.
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quick-debug-log-viewer-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Enqueue scripts for the admin area.
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/quick-debug-log-viewer-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Handle the download request for the debug log file.
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function admin_post_download_debug_log() {
		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You are not allowed to download the debug log.', 'quick-debug-log-viewer'));
		}
		global $wp_filesystem;
		if (empty($wp_filesystem)) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}
		
		if ($wp_filesystem->exists($this->debug_log_file_path)) {
			$log_content = $wp_filesystem->get_contents($this->debug_log_file_path);
			if ($log_content !== false) {
				header('Content-Type: text/plain');
				header('Content-Disposition: attachment; filename="debug.log"');
				echo $log_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				exit;
			} else {
				wp_die(esc_html__('Could not read debug log file.', 'quick-debug-log-viewer'));
			}
		} else {
			wp_die(esc_html__('Debug log file not found.', 'quick-debug-log-viewer'));
		}		
	}
}