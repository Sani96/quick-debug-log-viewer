<?php
if ( ! defined( 'ABSPATH' ) ) exit;
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
		$this->loader->add_action('admin_notices', $this, 'show_admin_notices');
		$this->loader->add_action('wp_ajax_quick_debug_log_viewer_admin_download_log', $this, 'quick_debug_log_viewer_admin_download_debug_log');
		$this->loader->add_action('wp_ajax_quick_debug_log_viewer_admin_clear_log', $this, 'quick_debug_log_viewer_admin_clear_log');
		$this->loader->add_action('wp_ajax_quick_debug_log_viewer_admin_search_debug_log', $this, 'quick_debug_log_viewer_admin_search_debug_log');
		$this->loader->add_action('wp_ajax_quick_debug_log_viewer_admin_load_more_debug_blocks', $this, 'quick_debug_log_viewer_admin_load_more_debug_blocks');
	}

    /**
     * Handle the POST request to clear the debug log.
     *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */

	public function quick_debug_log_viewer_admin_clear_log() {

		if (!current_user_can('manage_options')) {
			return;
		}

		check_ajax_referer('quick_debug_log_viewer_admin_clear_log_nonce', 'nonce');
		global $wp_filesystem;
		if (empty($wp_filesystem)) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$wp_filesystem->put_contents($this->debug_log_file_path, '', FS_CHMOD_FILE);
		$this->clear_success = true;
		wp_send_json_success([]);
	}

	/**
	 * Search the debug log for a specific keyword.
	 * 
	 * @since    1.0.3
	 * @access   public
	 * @return   void
	 * 
	 */
	public function quick_debug_log_viewer_admin_search_debug_log() {
		check_ajax_referer('quick_debug_log_viewer_admin_search_debug_log_nonce', 'nonce');
		$search_term = isset($_POST['keyword']) ? sanitize_text_field(wp_unslash($_POST['keyword'])) : '';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-quick-debug-log-viewer-log-reader.php';
		$blocks = Quick_Debug_Log_Viewer_Log_Reader::search_blocks($this->debug_log_file_path, $search_term);
		if (!$blocks || !is_array($blocks)) {
			wp_send_json_success([]);
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quick-debug-log-viewer-formatter.php';
		$formatted = Quick_Debug_Log_Viewer_Formatter::format_blocks($blocks);
		wp_send_json_success($formatted);
	}

	/**
	 * Load more debug log blocks via AJAX.
	 * 
	 * @since    1.0.4
	 * @access   public
	 * @return   void
	 */
	public function quick_debug_log_viewer_admin_load_more_debug_blocks() {
		check_ajax_referer('quick_debug_log_viewer_admin_load_more_debug_log_nonce', 'nonce');
		$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
		$limit  = 30;
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quick-debug-log-viewer-log-reader.php';
		if ( ! file_exists( $this->debug_log_file_path ) ) {
			wp_send_json_error( 'debug.log not found' );
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quick-debug-log-viewer-formatter.php';
		$blocks = Quick_Debug_Log_Viewer_Log_Reader::parse_blocks( $this->debug_log_file_path, $offset + $limit );
		$blocks = array_slice( $blocks, $offset, $limit );		
		$formatted = Quick_Debug_Log_Viewer_Formatter::format_blocks( $blocks );
		wp_send_json_success( $formatted );
		wp_send_json_success($formatted);
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
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-quick-debug-log-viewer-log-reader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-quick-debug-log-viewer-formatter.php';
		$raw_blocks = Quick_Debug_Log_Viewer_Log_Reader::parse_blocks($this->debug_log_file_path, 300);
		$blocks     = Quick_Debug_Log_Viewer_Formatter::format_blocks($raw_blocks);
		require_once plugin_dir_path(__FILE__) . 'partials/quick-debug-log-viewer-admin-display.php';
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
		wp_localize_script($this->plugin_name, 'quick_debug_log_viewer_admin_ajax', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		]);
	}

	/**
	 * Handle the download request for the debug log file.
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function quick_debug_log_viewer_admin_download_debug_log() {
		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You are not allowed to download the debug log.', 'quick-debug-log-viewer'));
		}
		check_ajax_referer('quick_debug_log_viewer_admin_download_debug_log_nonce', 'nonce');
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
				wp_send_json_success($log_content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				exit;
			} else {
				wp_die(esc_html__('Could not read debug log file.', 'quick-debug-log-viewer'));
			}
		} else {
			wp_die(esc_html__('Debug log file not found.', 'quick-debug-log-viewer'));
		}		
	}
}