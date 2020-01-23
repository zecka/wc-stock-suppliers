<?php

/**
 * WC_Product Additionnals fields.
 *
 * @since      1.0.0
 * @package    WCSS
 * @subpackage WCSS/includes
 * @author     Robin Ferrari <alert@robinferrari.ch>
 */

class WCSS_Product_CPT {
    /**
     * The unique identifier of this post type.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $post_type    The string used to uniquely identify the post type.
     */
    protected $post_type;

    /**
     * P¨fix for custom field.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $prefix    The string used to prefix custom fields
     */
    protected $prefix;

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
    

    public function __construct() {
        $this->post_type = 'product';
        $this->prefix    = WCSS_PREFIX;
    }

    public function run(){
        add_action('acf/init', [$this, 'product_fields']);
        // Add Variation Settings
        add_action('woocommerce_product_after_variable_attributes', [$this, 'product_variation_fields'], 10, 3);
        // Save Variation Settings
        add_action('woocommerce_save_product_variation', [$this, 'save_product_variation_fields'], 10, 2);
    }

    /**
     * Register the supplier post type
     *
     * @return void
     */
    public function register_post_type() {
        // Register Custom Supplier
        $labels = array(
            'name'           => _x('Suppliers', 'Supplier General Name', WCSS_SLUG),
            'singular_name'  => _x('Supplier', 'Supplier Singular Name', WCSS_SLUG),
            'menu_name'      => __('Suppliers', WCSS_SLUG),
            'name_admin_bar' => __('Supplier', WCSS_SLUG),
        );
        $args = array(
            'label'               => __('Supplier', WCSS_SLUG),
            'description'         => __('Supplier Description', WCSS_SLUG),
            'labels'              => $labels,
            'supports'            => array('title', 'custom-fields'),
            'taxonomies'          => array(),
            'hierarchical'        => false,
            'public'              => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
        );
        register_post_type($this->post_type, $args);

    }

    public function product_fields() {

        acf_add_local_field_group(array(
            'key' => $this->prefix.'group_5e189824bs233c',
            'title' => 'Supplier fields',
            'fields' => array(
                array(
                    'key' => 'field_5e1899c490788',
                    'label' => 'Supplier',
                    'name' => 'supplier',
                    'type' => 'post_object',
                    'post_type' => array(
                        0 => 'supplier',
                    ),
                    'taxonomy' => '',
                    'return_format' => 'id',
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_5e1899e790789',
                    'label' => esc_html__('Stock target value', WCSS_SLUG),
                    'name' => 'min_stock_target',
                    'type' => 'number',
                    'instructions' => esc_html__('Desired quantity to be kept continuously in stock', WCSS_SLUG),
                ),
                array(
                    'key' => WCSS_PREFIX.'field_supploer_ref',
                    'label' => esc_html__('Supplier ref', WCSS_SLUG),
                    'name' => 'supplier_ref',
                    'type' => 'text',
                    'instructions' => esc_html__('REF For supplier', WCSS_SLUG),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ),
                ),
            )
        ));
       
    }

    public function product_variation_fields($loop, $variation_data, $variation) {
        // Number Field
        woocommerce_wp_text_input(
            array(
                'id'                => $this->prefix . 'min_stock_target[' . $variation->ID . ']',
                'label'             => __('Stock target value', WCSS_SLUG),
                'desc_tip'          => 'true',
                'description'       => __('Desired quantity to be kept continuously in stock', WCSS_SLUG),
                'value'             => get_post_meta($variation->ID, $this->prefix . 'min_stock_target', true),
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0',
                ),
            )
        );

         // Number Field
        woocommerce_wp_text_input(
            array(
                'id'                => $this->prefix . 'supplier_ref[' . $variation->ID . ']',
                'label'             => __('Supplier ref', WCSS_SLUG),
                'desc_tip'          => 'true',
                'description'       => __('REF For supplier', WCSS_SLUG),
                'value'             => get_post_meta($variation->ID, $this->prefix . 'supplier_ref', true)
            )
        );
        
    }

    public function save_product_variation_fields($post_id) {
        // min_stock_target
        $min_stock_target = $_POST[$this->prefix . 'min_stock_target'][$post_id];
        if (!empty($min_stock_target)) {
            update_post_meta($post_id, $this->prefix . 'min_stock_target', esc_attr($min_stock_target));
        }
        $supplier_ref = $_POST[$this->prefix . 'supplier_ref'][$post_id];
        if (!empty($supplier_ref)) {
            update_post_meta($post_id, $this->prefix . 'supplier_ref', esc_attr($supplier_ref));
        }
    }
}