<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The file that defines the log reader class
 *
 * This class is responsible for reading and extracting PHP error blocks
 * from the WordPress debug.log file.
 *
 * @link       https://wpsani.store
 * @since      1.2.0
 *
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/includes
 */

/**
 * The class responsible for parsing and searching log blocks in debug.log
 *
 * Provides functionality to read the file in reverse, extract meaningful
 * error blocks, and optionally search for keywords inside them.
 *
 * @package    Quick_Debug_Log_Viewer
 * @subpackage Quick_Debug_Log_Viewer/includes
 * @author     WP Sani <federicosanua@gmail.com>
 */
 class Quick_Debug_Log_Viewer_Log_Reader {

    /**
     * Parses the debug log file and returns an array of error blocks.
     *
     * @since    1.2.0
     * @param    string  $file_path   Path to the debug.log file.
     * @param    int     $max_blocks  Maximum number of blocks to return.
     * @return   array                Array of error blocks.
     */
    public static function parse_blocks( $file_path, $max_blocks = 300, $max_lines = 5000 ) {
        if ( empty( $file_path ) ) return [];

        global $wp_filesystem;
        if ( empty( $wp_filesystem ) ) {
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }

        if ( ! $wp_filesystem->exists( $file_path ) ) return [];

        $content = $wp_filesystem->get_contents( $file_path );
        if ( $content === false ) return [];

        $lines = explode( "\n", $content );
        $lines = array_reverse( $lines );
        $lines = array_filter( array_map( 'trim', $lines ), fn($l) => $l !== '' );
        $lines = array_slice( $lines, 0, $max_lines );

        $blocks = [];
        $current_block = '';

        foreach ( $lines as $line ) {
            if ( preg_match( '/^\[\d{2}-[A-Za-z]{3}-\d{4} \d{2}:\d{2}:\d{2} UTC\]/', $line ) ) {
                if ( $current_block !== '' ) {
                    $blocks[] = trim( $line . "\n" . $current_block );
                    if ( count( $blocks ) >= $max_blocks ) break;
                    $current_block = '';
                } else {
                    $blocks[] = trim( $line );
                    if ( count( $blocks ) >= $max_blocks ) break;
                }
            } else {
                $current_block = $line . "\n" . $current_block;
            }
        }

        if ( $current_block !== '' && count($blocks) < $max_blocks ) {
            $blocks[] = trim( $current_block );
        }

        return $blocks;
    }

    /**
     * Searches for blocks in the debug log file that contain a specific keyword.
     * This method is case-insensitive and returns an array of blocks
     * that match the keyword.
     * 
     * @since    1.2.0
     * @param    string  $file_path   Path to the debug.log file.
     * @param    string  $keyword     Keyword to search for in the log blocks.
     * @param    int     $max_blocks  Maximum number of blocks to return.
     * @return   array                An array of matching log blocks.
     */
    public static function search_blocks( $file_path, $keyword = '', $max_blocks = 300 ) {
        $keyword = strtolower($keyword);
        $blocks = self::parse_blocks( $file_path, $max_blocks );

        if (empty($keyword)) return $blocks;

        return array_filter($blocks, function ($block) use ($keyword) {
            return strpos(strtolower($block), $keyword) !== false;
        });
    }
}
