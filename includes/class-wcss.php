<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://robinferrari.ch
 * @since      1.0.0
 *
 * @package    WCSS
 * @subpackage WCSS/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WCSS
 * @subpackage WCSS/includes
 * @author     Robin Ferrari <alert@robinferrari.ch>
 */
class WCSS {


	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WCSS_VERSION' ) ) {
			$this->version = WCSS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = WCSS_SLUG;

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once dirname(__FILE__) . '/class-tgm-plugin-activation.php';
		require_once dirname(__FILE__) . '/class-wcss-supplier-mail.php';
		require_once dirname(__FILE__) . '/class-wcss-admin.php';
		require_once dirname(__FILE__) . '/class-wcss-helpers.php';
		require_once dirname(__FILE__) . '/class-wcss-ajax.php';
		require_once dirname(__FILE__) . '/class-wcss-supplier-cpt.php';
		require_once dirname(__FILE__) . '/class-wcss-supplier-order-cpt.php';
		require_once dirname(__FILE__) . '/class-wcss-supplier-order.php';
		require_once dirname(__FILE__) . '/class-wcss-supplier-order-product.php';
		require_once dirname(__FILE__) . '/class-wcss-product-cpt.php';
		require_once dirname(__FILE__) . '/class-wcss-tool.php';
		require_once WCSS_Helpers::get_plugin_path() . 'partials/supplier-order-item.php';
	}

	/**
	 * Register the required plugin
	 * @since    1.0.0
	 * @return void
	 */
	public function required_plugins(){
		$plugins = array(
			array(
				'name'        => 'Advanced Custom Fields PRO',
				'slug'        => 'advanced-custom-fields-pro',
				'required'  => true,
			),
			array(
				'name'      => 'Github Updater',
				'slug'      => 'github-updater',
				'source'    => 'https://github.com/afragen/github-updater/archive/master.zip',
				'required'  => false,
			),
			array(
				'name'      => 'WooCommerce',
				'slug'      => 'woocommerce',
				'required'  => true,
			),
		);
		$config  = array(
			'id' => $this->plugin_name,
		);

		tgmpa( $plugins, $config );
	}




	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		add_action('tgmpa_register', [$this, 'required_plugins']);
		
		$admin_page = new WCSS_Admin();
		$supplier_cpt = new WCSS_Supplier_CPT($this->get_plugin_name(), $this->get_version());
		$supplier_order_cpt = new WCSS_Supplier_Order_CPT($this->get_plugin_name(), $this->get_version());
		$product_cpt = new WCSS_Product_CPT($this->get_plugin_name(), $this->get_version());
		$tool_page = new WCSS_Tool($this->get_plugin_name(), $this->get_version());

		$admin_page->run();
		$supplier_cpt->run();
		$supplier_order_cpt->run();
		$product_cpt->run();
		$tool_page->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
