<?php
class WCSS_Admin {
    public function __construct() {
    }
    public function run() {
        $this->register_settings_fields();
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('acf/init', [$this, 'option_page']);
        add_action('acf/init', [$this, 'smtp_configuration']);
        add_action('admin_init', [$this, 'add_capability']);

    }
    function add_capability(){
        $role = get_role('administrator');
        $role->add_cap('publish_stocksupplier', true);
        $role->add_cap('edit_stocksupplier', true);
        $role->add_cap('edit_others_stocksupplier', true);
        $role->add_cap('read_private_stocksupplier', true);
        $role->add_cap('edit_stocksupplier', true);
        $role->add_cap('delete_stocksupplier', true);
        $role->add_cap('read_stocksupplier', true);
        $role->add_cap('wcss_stock_supplier', true);

    }
    function smtp_configuration(){
        $settings = get_field('wcss_plugin_settings', 'options');
        if(isset($settings['smtp_enable']) && $settings['smtp_enable']){
            add_action('phpmailer_init', [$this, 'php_mailer_configuration']);
        }
    }
    public function php_mailer_configuration(PHPMailer $phpmailer){
        $settings = get_field('wcss_plugin_settings', 'options');
        $prefix ='smtp_';
        if($settings[$prefix.'host']){
            $phpmailer->Host = $settings[$prefix.'host'];
        }
        if($settings[$prefix.'port']){
            $phpmailer->Port       = $settings[$prefix.'port'];
        }
        if($settings[$prefix.'user']){
            $phpmailer->Username   = $settings[$prefix.'user']; // if required
        }
        if($settings[$prefix.'password']){
            $phpmailer->Password   = $settings[$prefix.'password']; // if required
        }
        $phpmailer->SMTPAuth   = $settings['smtp_auth']; // if required
        if($settings[$prefix.'secure']!=='none'){
            $phpmailer->SMTPSecure = $settings[$prefix.'secure']; // 'ssl' or 'tls'  or delete this line
        }
        $phpmailer->IsSMTP();
    }
    function admin_menu() {
        add_menu_page(
            __('Stock Suppliers', WCSS_SLUG),
            __('Stock Suppliers', WCSS_SLUG),
            WCSS_CAPABILITY,
            WCSS_SLUG,
            '', // Callback, leave empty
            'dashicons-store',
            2// Position
        );
    }
    public function option_page() {
        
        // Check function exists.
        if (function_exists('acf_add_options_page')) {
            // Register options page.
            acf_add_options_page(array(
                'page_title'  => __('Stock Supplier Settings', WCSS_SLUG),
                'menu_title'  => __('Settings', WCSS_SLUG),
                'menu_slug'   => WCSS_SLUG . '-settings',
                'capability'  => WCSS_CAPABILITY,
                'parent_slug' => WCSS_SLUG,
                'redirect'    => false,
            ));
        }
    }
    private function register_settings_fields() {
        if (function_exists('acf_add_local_field_group')):
            acf_add_local_field_group(array(
                'key'                   => 'group_5e288d3bb37f3',
                'title'                 => 'WCSS',
                'fields'                => array(
                    array(
                        'key'                 => 'field_5e288d4143a6c',
                        'label'               => 'Settings',
                        'name'                => WCSS_PREFIX.'plugin_settings',
                        'type'                => 'group',
                        'instructions'        => '',
                        'required'            => 0,
                        'conditional_logic'   => 0,
                        'wrapper'             => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'layout'              => 'block',
                        'wpml_cf_preferences' => 0,
                        'sub_fields'          => array(
                            array(
                                'key'                 => 'field_5e288d9643a6d',
                                'label'               => 'Email',
                                'name'                => '',
                                'type'                => 'tab',
                                'wrapper'             => array(
                                    'width' => '',
                                    'class' => '',
                                    'id'    => '',
                                ),
                                'placement'           => 'top',
                                'endpoint'            => 0,
                                'wpml_cf_preferences' => 0,
                            ),
                            array(
                                'key'                 => WCSS_PREFIX.'field_disable_mail_sending',
                                'label'               => __('Disable mail sending from WordPress', WCSS_SLUG),
                                'name'                => 'disable_mail_sending',
                                'type'                => 'true_false',
                                'ui'                  =>  1,
                                'required'            => 0,
                                'default_value'       => 0,
                            ),
                            array(
                                'key'                 => 'field_5e288e1d43a71',
                                'label'               => 'From name',
                                'name'                => 'from_name',
                                'type'                => 'text',
                                'required'            => 1,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),

                            ),
                            array(
                                'key'                 => 'field_5e288db543a6e',
                                'label'               => 'From email',
                                'name'                => 'from_email',
                                'type'                => 'email',
                                'instructions'        => __('use email with @yourdomain.com to prevent email go on spam', WCSS_SLUG),
                                'required'            => 1,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),
                
                            ),
                            array(
                                'key'                 => 'field_5e288dd843a6f',
                                'label'               => 'Reply to name',
                                'name'                => 'replyto_name',
                                'type'                => 'text',
                                'required'            => 0,
                                'conditional_logic'   => 0,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),
                            ),
                            array(
                                'key'                 => 'field_5e288e0143a70',
                                'label'               => 'Reply to email',
                                'name'                => 'replyto_email',
                                'type'                => 'email',
                                'required'            => 0,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),
                            ),
                            array(
                                'key'                 => 'field_email_copy_address',
                                'label'               => 'Send a copy to',
                                'name'                => 'cc_to',
                                'type'                => 'email',
                                'required'            => 0,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),
                            ),
                            array(
                                'key'                 => 'field_5e288e2c43a72',
                                'label'               => 'Subject',
                                'name'                => 'subject',
                                'type'                => 'text',
                                'instructions'        => __('You can use %%title%%, %%first_name%% and %%last_name%%variable', WCSS_SLUG),
                                'required'            => 1,
                                'wrapper'             => array(
                                    'width' => '',
                                ),
                                'wpml_cf_preferences' => 0,
                            ),
                            array(
                                'key'                 => 'field_5e288e3543a73',
                                'label'               => 'Before content',
                                'name'                => 'before_content',
                                'type'                => 'wysiwyg',
                                'instructions'        => __('You can use %%title%%, %%first_name%% and %%last_name%%variable', WCSS_SLUG),
                                'required'            => 0,
                                'wrapper'             => array(
                                    'width' => '',
                                ),
                                'tabs'                => 'all',
                                'toolbar'             => 'full',
                                'media_upload'        => 0,
                                'delay'               => 0,
                            ),
                            array(
                                'key'                 => 'field_5e288e3c43a74',
                                'label'               => 'After content',
                                'name'                => 'after_content',
                                'type'                => 'wysiwyg',
                                'instructions'        => __('You can use %%title%%, %%first_name%% and %%last_name%%variable', WCSS_SLUG),
                                'required'            => 0,
                                'wrapper'             => array(
                                    'width' => '',
                                ),
                                'tabs'                => 'all',
                                'toolbar'             => 'full',
                                'media_upload'        => 0,
                                'delay'               => 0,
                            ),
                            array(
                                'key'                 => WCSS_PREFIX.'field_smtp_tab',
                                'label'               => 'SMTP',
                                'name'                => '',
                                'type'                => 'tab',
                                'wrapper'             => array(
                                    'width' => '',
                                    'class' => '',
                                    'id'    => '',
                                ),
                                'placement'           => 'top',
                                'endpoint'            => 0,
                                'wpml_cf_preferences' => 0,
                            ),
                            array(
                                'key'                 => WCSS_PREFIX.'field_smtp_enable',
                                'label'               => __('Configure wp_mail with smtp', WCSS_SLUG),
                                'name'                => 'smtp_enable',
                                'type'                => 'true_false',
                                'ui'                  => 1,
                                'default_value'       => 0,
                                'required'            => 0,
                                'conditional_logic'   => 0
                            ),
                            array(
                                'key'                 => WCSS_PREFIX.'field_smtp_host',
                                'label'               => __('host', WCSS_SLUG),
                                'name'                => 'smtp_host',
                                'type'                => 'text',
                                'required'            => 0,
                                'conditional_logic'   => 0,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),
                            ),
                            
                            array(
                                'key'                 => WCSS_PREFIX.'field_smtp_port',
                                'label'               => __('port', WCSS_SLUG),
                                'name'                => 'smtp_port',
                                'type'                => 'number',
                                'required'            => 0,
                                'conditional_logic'   => 0,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),
                            ),
                            array(
                                'key'                 => WCSS_PREFIX.'field_smtp_username',
                                'label'               => __('username', WCSS_SLUG),
                                'name'                => 'smtp_user',
                                'type'                => 'text',
                                'required'            => 0,
                                'conditional_logic'   => 0,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),
                            ),
                            array(
                                'key'                 => WCSS_PREFIX.'field_smtp_auth',
                                'label'               => __('Auth', WCSS_SLUG),
                                'name'                => 'smtp_auth',
                                'type'                => 'true_false',
                                'ui'                  => 1,
                                'default_value'       => 1,
                                'required'            => 0,
                                'conditional_logic'   => 0,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),
                            ),
                            array(
                                'key'                 => WCSS_PREFIX.'field_smtp_password',
                                'label'               => __('password', WCSS_SLUG),
                                'name'                => 'smtp_password',
                                'type'                => 'password',
                                'required'            => 0,
                                'conditional_logic'   => 0,
                                'wrapper'             => array(
                                    'width' => '50',
                                ),
                            ),
                            array(
                                'key'                 => WCSS_PREFIX.'field_smtp_secure',
                                'label'               => __('security', WCSS_SLUG),
                                'name'                => 'smtp_secure',
                                'type'                => 'select',
                                'choices'             => array(
                                    'none'      => __('None'),
                                    'ssl'       => __('SSL'),
                                    'tls'       => __('TLS'),
                                ),
                                'default_value' => array(
                                    0 => 'none',
                                ),
                                'return_format' => 'value',
                            ),
                            
                        ),
                    ),
                ),
                'location'              => array(
                    array(
                        array(
                            'param'    => 'options_page',
                            'operator' => '==',
                            'value'    => WCSS_SLUG.'-settings',
                        ),
                    ),
                ),
                'menu_order'            => 0,
                'position'              => 'normal',
                'style'                 => 'seamless',
                'label_placement'       => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen'        => '',
                'active'                => true,
                'description'           => '',
            ));

        endif;
    }
}