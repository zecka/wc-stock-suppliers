<?php
/*
 * Plugin Name:       WC Stock Suppliers
 * Plugin URI:        https://robinferrari.ch
 * GitHub Plugin URI: zecka/wc-stock-suppliers
 * Description:       Generate Stock report by supplier and send order to supplier
 * Version:           1.1.4
 * Author:            Robin Ferrari
 * Author URI:        https://robinferrari.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-stock-suppliers
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WCSS_VERSION', '1.0.3' );
define( 'WCSS_SLUG', 'wc-stock-suppliers' );
define( 'WCSS_PREFIX', 'wcss_' );
define( 'WCSS_CAPABILITY', 'wcss_stock_supplier' );
define( 'WCSS_URL', plugin_dir_url(__FILE__));
define( 'WCSS_PATH', plugin_dir_path(__FILE__) );




/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wcss-activator.php
 */
function activate_wc_stock_supplier() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcss-activator.php';
	WCSS_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wcss-deactivator.php
 */
function deactivate_wc_stock_supplier() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcss-deactivator.php';
	WCSS_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_stock_supplier' );
register_deactivation_hook( __FILE__, 'deactivate_wc_stock_supplier' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wcss.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_stock_supplier() {

	$plugin = new WCSS();
	$plugin->run();

}
run_wc_stock_supplier();
