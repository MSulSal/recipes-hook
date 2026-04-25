<?php
/**
 * Plugin Name: Recipe PDF Library
 * Description: A simple recipe library where each recipe is an uploaded PDF.
 * Version: 0.1.0
 * Author: Recipe PDF Library
 * Text Domain: recipe-pdf-library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RPL_VERSION', '0.1.0' );
define( 'RPL_PLUGIN_FILE', __FILE__ );
define( 'RPL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RPL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once RPL_PLUGIN_DIR . 'includes/post-types.php';
require_once RPL_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once RPL_PLUGIN_DIR . 'includes/shortcodes.php';
require_once RPL_PLUGIN_DIR . 'includes/detail-view.php';

function rpl_activate(): void {
	rpl_register_post_type();
	rpl_register_taxonomies();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rpl_activate' );

function rpl_deactivate(): void {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rpl_deactivate' );

function rpl_init(): void {
	rpl_register_post_type();
	rpl_register_taxonomies();
}
add_action( 'init', 'rpl_init' );
