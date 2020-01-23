<?php

/**
 * The Custom post type for the supplier.
 *
 * @since      1.0.0
 * @package    WCSS
 * @subpackage WCSS/includes
 * @author     Robin Ferrari <alert@robinferrari.ch>
 */

class WCSS_Supplier_CPT{
    /**
	 * The unique identifier of this post type.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $post_type    The string used to uniquely identify the post type.
	 */
    protected $post_type;
    
    /**
	 * Prefix for custom field.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $prefix    The string used to prefix custom fields
	 */
    protected $prefix;
    

    public function __construct() {
        $this->post_type    = 'supplier';
        $this->prefix       = WCSS_PREFIX;
    }
    public function run(){
        add_action('init', [$this, 'register_post_type'], 0);
        add_action('acf/init', [$this, 'supplier_fields'], 0);
    }

    /**
     * Register the supplier post type
     *
     * @return void
     */
    public function register_post_type(){
            // Register Custom Supplier
        $labels = array(
            'name'                  => _x('Suppliers', 'Supplier General Name', WCSS_SLUG),
            'singular_name'         => _x('Supplier', 'Supplier Singular Name', WCSS_SLUG),
            'menu_name'             => __('Suppliers', WCSS_SLUG),
            'name_admin_bar'        => __('Supplier', WCSS_SLUG),
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
            'show_in_menu'        => WCSS_SLUG,
        );
        register_post_type($this->post_type, $args);

    }

    public function supplier_fields(){
        acf_add_local_field_group(array(
            'key' => $this->prefix.'suppliers_fields',
            'title' => 'Supplier Info',
            'fields' => array(
                array(
                    'key' => $this->prefix.'suppliers_email',
                    'description'=> __('Emails separated by a comma'),
                    'label' => 'email',
                    'name' => 'email',
                    'type' => 'text',
                ),
                array(
                    'key' => $this->prefix.'contact_first_name',
                    'label' => __('Contact first name', WCSS_SLUG),
                    'name' => 'contact_firstname',
                    'type' => 'text',
                ),
                array(
                    'key' => $this->prefix.'contact_last_name',
                    'label' => __('Contact last name', WCSS_SLUG),
                    'name' => 'contact_lastname',
                    'type' => 'text',
                ),
                array(
                    'key'                 => 'field_supplier_custom_email',
                    'label'               => __('Customize email content', WCSS_SLUG),
                    'name'                => 'custom_email',
                    'type'                => 'true_false',
                    'instructions'        => __('Overwrite the default values of the emails defined in the parameters',WCSS_SLUG),
                    'required'            => 0,
                    'wrapper'             => array(
                        'width' => '',
                    ),
                    'default_value'       => 0,
                    'ui'                  => 1,
                    'ui_on_text'          => __('Yes', WCSS_SLUG),
                    'ui_off_text'         => __('No', WCSS_SLUG),
                    'wpml_cf_preferences' => 0,
                ),
                array(
                    'key'                 => 'field_supplier_custom_subject',
                    'label'               => __('Subject', WCSS_SLUG),
                    'name'                => 'subject',
                    'type'                => 'text',
                    'instruction'         => __('You can use %%title%%, %%first_name%% and %%last_name%%variable', WCSS_SLUG),
                    'required'            => 0,
                    'conditional_logic'   => array(
                        array(
                            array(
                                'field'    => 'field_supplier_custom_email',
                                'operator' => '==',
                                'value'    => '1',
                            ),
                        ),
                    ),
                    'wrapper'             => array(
                        'width' => '',
                    ),
                ),
                array(
                    'key'                 => 'field_supplier_custom_before_content',
                    'label'               => __('Before Content', WCSS_SLUG),
                    'name'                => 'before_content',
                    'type'                => 'wysiwyg',
                    'instructions'        => __('You can use %%title%%, %%first_name%% and %%last_name%%variable', WCSS_SLUG),
                    'required'            => 0,
                    'conditional_logic'   => array(
                        array(
                            array(
                                'field'    => 'field_supplier_custom_email',
                                'operator' => '==',
                                'value'    => '1',
                            ),
                        ),
                    ),
                    'wrapper'             => array(
                        'width' => '',
                    ),
                    'default_value'       => '',
                    'tabs'                => 'all',
                    'toolbar'             => 'full',
                    'media_upload'        => 0,
                ),
                array(
                    'key'                 => 'field_supplier_custom_after_content',
                    'label'               => __('After content', WCSS_SLUG),
                    'name'                => 'after_content',
                    'type'                => 'wysiwyg',
                    'instructions'        => __('You can use %%title%%, %%first_name%% and %%last_name%%variable', WCSS_SLUG),
                    'required'            => 0,
                    'conditional_logic'   => array(
                        array(
                            array(
                                'field'    => 'field_supplier_custom_email',
                                'operator' => '==',
                                'value'    => '1',
                            ),
                        ),
                    ),
                    'wrapper'             => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                    'default_value'       => '',
                    'tabs'                => 'all',
                    'toolbar'             => 'full',
                    'media_upload'        => 1,
                    'delay'               => 0,
                    'wpml_cf_preferences' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'supplier',
                    ),
                ),
            ),
        ));
    }

}