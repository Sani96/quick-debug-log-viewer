<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpsani.store
 * @since      1.0.0
 *
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/public
 * @author     WP Sani <federicosanua@gmail.com>
 */
class Quick_Debug_Log_Viewer_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Quick_Debug_Log_Viewer_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	private $debug_log_file_path;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $loader = null, $debug_log_file_path = null ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->loader = $loader;
        $this->loader->add_action( 'wp_footer', $this, 'render_debug_fab' );
		$this->debug_log_file_path = $debug_log_file_path ? $debug_log_file_path : WP_CONTENT_DIR . '/debug.log';
		$this->loader->add_action('wp_ajax_search_debug_log', $this, 'search_debug_log');
		$this->setup_public_hooks();
	}

	/**
     * Setup all public hooks cleanly.
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @return   void
     */
	public function setup_public_hooks() {
		$this->loader->add_action('wp_ajax_quick_debug_log_viewer_public_load_more_debug_blocks', $this, 'quick_debug_log_viewer_public_load_more_debug_blocks');
		$this->loader->add_action('wp_ajax_quick_debug_log_viewer_public_get_log_frontend', $this, 'quick_debug_log_viewer_public_get_log_frontend_callback' );
		$this->loader->add_action('wp_ajax_quick_debug_log_viewer_public_download_debug_log', $this, 'quick_debug_log_viewer_public_download_debug_log');
		$this->loader->add_action('wp_ajax_quick_debug_log_viewer_public_clear_debug_log', $this, 'quick_debug_log_viewer_public_clear_debug_log');
		$this->loader->add_action('wp_ajax_quick_debug_log_viewer_public_search_debug_log', $this, 'quick_debug_log_viewer_public_search_debug_log');
	}

	public function quick_debug_log_viewer_public_download_debug_log() {
		check_ajax_referer('quick_debug_log_viewer_public_download_log_nonce', 'nonce');
		if ( ! current_user_can('manage_options') ) {
			wp_send_json_error('Unauthorized');
		}
		if ( ! file_exists($this->debug_log_file_path) ) {
			wp_send_json_error('Log file not found.');
		}
		$content = file_get_contents($this->debug_log_file_path);
		wp_send_json_success([
			'filename' => 'debug.log',
			'content'  => $content
		]);
	}

	public function quick_debug_log_viewer_public_clear_debug_log() {
		check_ajax_referer('quick_debug_log_viewer_public_clear_log_nonce', 'nonce');
		if ( ! current_user_can('manage_options') ) {
			wp_send_json_error('Unauthorized');
		}
		if ( file_exists($this->debug_log_file_path) ) {
			file_put_contents($this->debug_log_file_path, '');
			wp_send_json_success('Log cleared.');
		} else {
			wp_send_json_error('Log file not found.');
		}
	}

	/**
	 * Render the debug FAB (Floating Action Button) and modal for viewing debug.log.
	 * This function outputs the necessary HTML and inline styles for the FAB and modal.
	 * It checks if the current user has the capability to manage options before rendering.
	 * 	
	 * @since    1.0.0
	 * @return   void
	 */
	public function render_debug_fab() {
		if ( ! current_user_can( 'manage_options' ) ) return;
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/quick-debug-log-viewer-public-display.php';
	}

	/**
	 * Callback function for the AJAX request to get the log file content.
	 * This function checks the nonce for security, verifies user permissions,
	 * and reads the log file.
	 * 
	 * @since    1.0.4
	 * @access   public
	 * @return   void
	 */
	function quick_debug_log_viewer_public_get_log_frontend_callback() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quick-debug-log-viewer-log-reader.php';
		if ( ! file_exists( $this->debug_log_file_path ) ) {
			wp_send_json_error( 'debug.log not found' );
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quick-debug-log-viewer-formatter.php';
		$blocks = Quick_Debug_Log_Viewer_Log_Reader::parse_blocks( $this->debug_log_file_path, 300 );
		$formatted = Quick_Debug_Log_Viewer_Formatter::format_blocks( $blocks );
		wp_send_json_success( $formatted );
	}

		/**
	 * Search the debug log for a specific keyword.
	 * 
	 * @since    1.2.0
	 * @access   public
	 * @return   void
	 * 
	 */
	public function quick_debug_log_viewer_public_search_debug_log() {
		check_ajax_referer('quick_debug_log_viewer_public_search_debug_log_nonce', 'nonce');
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
	 * @since    1.2.0
	 * @access   public
	 * @return   void
	 */
	public function quick_debug_log_viewer_public_load_more_debug_blocks() {
		check_ajax_referer('quick_debug_log_viewer_public_load_more_debug_log_nonce', 'nonce');
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
	}



	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Quick_Debug_Log_Viewer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Quick_Debug_Log_Viewer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/quick-debug-log-viewer-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Quick_Debug_Log_Viewer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Quick_Debug_Log_Viewer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/quick-debug-log-viewer-public.js', array( 'jquery' ), $this->version, true );
		wp_localize_script($this->plugin_name, 'quick_debug_log_viewer_public_ajax', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'quick_debug_log_viewer_frontend_log_nonce' ),
		]);
	}

}
