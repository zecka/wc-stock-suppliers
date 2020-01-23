<?php
class WCSS_Tool{


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
     * Prefix for custom field.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $prefix    The string used to prefix custom fields
     */
    protected $prefix;
    
    public function __construct($plugin_name, $version){
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->prefix = WCSS_PREFIX;
    }
    public function run() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
        add_action('admin_menu', array($this, 'admin_menu'));
        $ajax_actions = new WCSS_Ajax();
        $ajax_actions->run();
    }
    public function is_edit_supplier_order(){
        $screen = get_current_screen();
        return ($screen->base=='post' && $screen->id=='supplier_order');
    }
    public function is_supplier_tool(){
        return (isset($_GET['page']) && $_GET['page']===WCSS_SLUG.'-generate');
    }
    public function enqueue(){
        if($this->is_supplier_tool() || $this->is_edit_supplier_order()){
            $this->enqueue_scripts();
            $this->enqueue_styles();
        }
    }
    public function enqueue_scripts(){
        wp_enqueue_script($this->plugin_name, WCSS_Helpers::get_plugin_url() . '/assets/build/js/app.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'ajaxurl', admin_url('admin-ajax.php'));
    }
    public function enqueue_styles(){
        wp_enqueue_style($this->plugin_name, WCSS_Helpers::get_plugin_url(). 'assets/build/css/bundle.css', false, $this->version);
    }
    public function admin_menu() {
         $hook = add_submenu_page(
             WCSS_SLUG, // parent page
              __('Generate supplier order', WCSS_SLUG), // page title
             __('Generate supplier order', WCSS_SLUG), // menu title
             'install_plugins', // Capability
             WCSS_SLUG.'-generate',  // menu slug
             array($this, 'admin_page') // callback
        );
        add_action("load-$hook", array($this, 'admin_page_load'));
    }

    public function admin_page_load() {
        // ...
    }

    public function admin_page() {
        require_once WCSS_Helpers::get_plugin_path() . 'partials/suppliers-order-generate.php';
    }

    
}