<?php
function custom_meta_box_markup() {

}

/**
 * The Custom post type for the supplier.
 *
 * @since      1.0.0
 * @package    WCSS
 * @subpackage WCSS/includes
 * @author     Robin Ferrari <alert@robinferrari.ch>
 */

class WCSS_Supplier_Order_CPT {
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
        $this->post_type = 'supplier_order';
        $this->prefix    = WCSS_PREFIX;
    }

    public function run() {
        add_action('init', [$this, 'register_post_type'], 0);
        add_action('acf/init', [$this, 'supplier_order_fields']);
        add_action("add_meta_boxes", [$this, "add_order_status_meta_box"]);
        add_action("acf/save_post", [$this, "after_acf_save_post"], 100, 1);
        add_filter('manage_'.$this->post_type.'_posts_columns', [$this, 'set_admin_columns']);
        add_filter('manage_edit-'.$this->post_type.'_sortable_columns', [$this, 'admin_sortable_columns']);
        add_action('manage_'.$this->post_type.'_posts_custom_column', [$this, 'admin_columns'], 10, 2);

        $this->hidden_fields();
    }
    public function admin_sortable_columns($columns){
        $columns['order_status'] = __('Order status', WCSS_SLUG);
        return $columns;
    }
    public function set_admin_columns($columns){
        $columns['order_status'] = __('Order status', WCSS_SLUG);
        return $columns;
    }
    public function admin_columns($column, $post_id){
        if($column==='order_status'){
            $status = get_field('order_status', $post_id);
            $order = 3;
            $text = __('Complete', WCSS_SLUG);
            if($status=='pending'){
                $order=1;
                $text = __('Pending', WCSS_SLUG);
            }elseif($status == 'sent'){
                $order=2;
                $text = __('Email Sent', WCSS_SLUG);
            }
            echo '<span title="'.$order.'">';
            echo $text;
            echo '</span>';
        }
    }
    /**
     * Register the supplier post type
     *
     * @return void
     */
    public function register_post_type() {
        // Register Custom Supplier
        $labels = array(
            'name'           => _x('Supplier Orders', 'Supplier Orders General Name', WCSS_SLUG),
            'singular_name'  => _x('Supplier Order', 'Supplier Order Singular Name', WCSS_SLUG),
            'menu_name'      => __('Supplier Orders', WCSS_SLUG),
            'name_admin_bar' => __('Supplier Order', WCSS_SLUG),
        );
        $args = array(
            'label'               => __('Supplier Order', WCSS_SLUG),
            'description'         => __('Supplier Order Description', WCSS_SLUG),
            'labels'              => $labels,
            'supports'            => array('title', 'custom-fields'),
            'taxonomies'          => array(),
            'hierarchical'        => false,
            'public'              => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_in_menu'        => WCSS_SLUG,
            'capability_type' => 'stocksupplier',
            'capabilities' => array(
                'publish_posts' => 'publish_stocksupplier',
                'edit_posts' => 'edit_stocksupplier',
                'edit_others_posts' => 'edit_others_stocksupplier',
                'read_private_posts' => 'read_private_stocksupplier',
                'edit_post' => 'edit_stocksupplier',
                'delete_post' => 'delete_stocksupplier',
                'read_post' => 'read_stocksupplier',
            ),

        );
        register_post_type($this->post_type, $args);

    }

    public function supplier_order_fields() {
        acf_add_local_field_group(array(
            'key'      => $this->prefix . 'group_supplier_email_preview',
            'title'    => __('Supplier Email preview', WCSS_SLUG),
            'location' => array(
                array(
                    array(
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'supplier_order',
                    ),
                ),
            ),
            'menu_order' => 1,
            'position'   => 'acf_after_title',
        ));
        acf_add_local_field_group(array(
            'key'      => $this->prefix . 'group_supplier_order',
            'title'    => __('Supplier Order', WCSS_SLUG),
            'fields'   => array(
                array(
                    'key'           => $this->prefix . 'order_supplier',
                    'label'         => __('Supplier', WCSS_SLUG),
                    'name'          => 'supplier',
                    'type'          => 'post_object',
                    'post_type'     => array(
                        0 => 'supplier',
                    ),
                    'return_format' => 'id',
                ),
                array(
                    'key'           => $this->prefix . 'supplier_order_status',
                    'label'         => __('Order Status', WCSS_SLUG),
                    'name'          => 'order_status',
                    'type'          => 'select',
                    'choices'       => array(
                        'pending'  => 'Pending',
                        'sent'     => 'Email Sent',
                        'complete' => 'Complete',
                    ),
                    'default_value' => array(
                        0 => 'pending',
                    ),
                    'return_format' => 'value',
                ),
                array(
                    'key'        => $this->prefix . 'supplier_order_items',
                    'label'      => 'Order items',
                    'name'       => 'order_items',
                    'type'       => 'repeater',
                    'collapsed'  => $this->prefix . 'order_item_product',
                    'layout'     => 'block',
                    'sub_fields' => array(
                        array(
                            'key'           => $this->prefix . 'supplier_order_item_product',
                            'label'         => 'Product',
                            'name'          => 'product',
                            'instructions'  => __('INFO: Variation name start with "-" (Product, -Product Variation)', WCSS_SLUG),
                            'type'          => 'post_object',
                            'wrapper'       => array(
                                'width' => '80',
                            ),
                            'post_type'     => array(
                                0 => 'product',
                                1 => 'product_variation',
                            ),
                            'return_format' => 'id',
                        ),
                        array(
                            'key'               => $this->prefix . 'supplier_order_item_quantity',
                            'label'             => __('Quantity', WCSS_SLUG),
                            'name'              => 'quantity',
                            'type'              => 'number',
                            'instructions'      => __('Quantity to reorder', WCSS_SLUG),
                            'required'          => 0,
                            'conditional_logic' => 0,
                            'wrapper'           => array(
                                'width' => '20',
                            ),
                        ),
                    ),
                ),
                array(
                    'key'                 => 'field_5e2893ab5d99a',
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
                    'key'                 => 'field_5e2893cb5d99b',
                    'label'               => __('Subject', WCSS_SLUG),
                    'name'                => 'subject',
                    'type'                => 'text',
                    'instruction'         => __('You can use %%title%%, %%first_name%% and %%last_name%%variable', WCSS_SLUG),
                    'required'            => 0,
                    'conditional_logic'   => array(
                        array(
                            array(
                                'field'    => 'field_5e2893ab5d99a',
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
                    'key'                 => 'field_5e2893db5d99c',
                    'label'               => __('Before Content', WCSS_SLUG),
                    'name'                => 'before_content',
                    'type'                => 'wysiwyg',
                    'instructions'        => __('You can use %%title%%, %%first_name%% and %%last_name%%variable', WCSS_SLUG),
                    'required'            => 0,
                    'conditional_logic'   => array(
                        array(
                            array(
                                'field'    => 'field_5e2893ab5d99a',
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
                    'key'                 => 'field_5e2893e55d99d',
                    'label'               => __('After content', WCSS_SLUG),
                    'name'                => 'after_content',
                    'type'                => 'wysiwyg',
                    'instructions'        => __('You can use %%title%%, %%first_name%% and %%last_name%%variable', WCSS_SLUG),
                    'required'            => 0,
                    'conditional_logic'   => array(
                        array(
                            array(
                                'field'    => 'field_5e2893ab5d99a',
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
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'supplier_order',
                    ),
                ),
            ),
            'position' => 'acf_after_title',
            'menu_order' => 5,
        ));

    }
    public function hidden_fields() {
        add_filter('acf/prepare_field/key='.$this->prefix . 'supplier_order_status', [$this, 'hide_field']);
        add_filter('acf/prepare_field/key='.$this->prefix . 'order_supplier', [$this, 'hide_field']);
        add_filter('acf/prepare_field/key='.$this->prefix . 'supplier_order_items', [$this, 'lock_order_items']);
    }
    public function hide_field() {
        return false;
    }
    public function lock_order_items($field) {
        $status=get_field('order_status');
        if($status=='sent' || $status=='complete'){
            echo '<div id="wcss-email-reminder">';
           echo '<h1>'.__('Email Reminder:', WCSS_SLUG).'</h1>';
           the_field('_email_reminder');
           echo '</div>';
           return $field;
        }else{
            return $field;
        }
    }
    public function add_order_status_meta_box() {
        add_meta_box("wcss-supplier-order-actions", __('Supplier Order Actions', WCSS_SLUG), [$this, "order_status_meta_box"], $this->post_type, "side", "high", null);
    }
    public function order_status_meta_box() {
        /*
        'pending'  => 'Pending',
        'sent'     => 'Email Sent',
        'complete' => 'Complete',
         */
        $order_status = get_field('order_status');
        $supplier     = get_field('supplier');
        ?>
        <ul>
            <li><strong>Supplier:</strong> <?php echo get_the_title($supplier); ?></li>
            <li><strong>Status:</strong> <?php echo $order_status; ?></li>
        </ul>
        <?php
        $btn_class = 'button-primary';
        switch ($order_status) {
            case 'pending':
                $btn_label  = __('Send email', WCSS_SLUG);
                $btn_action = 'send_email';
                break;
            case 'sent':
                $btn_label  = __('Update Stocks', WCSS_SLUG);
                $btn_action = 'increase_stock';
                break;
            case 'complete':
                $btn_label  = __('Cancel Stocks Update', WCSS_SLUG);
                $btn_action = 'decrease_stock';
                $btn_class .= ' wcss-button-danger';
                break;
            default:
                $btn_label  = false;
                $btn_action = false;
        }
        if ($btn_action) {
            ?>
            <input id="wcss-action-value" type="hidden" value="none" name="<?php echo $this->prefix; ?>actions" />
            <button id="wcss-action-submit" data-action="<?php echo $btn_action; ?>" class="<?php echo $btn_class ?>"><?php echo $btn_label; ?></button>
            <?php if($btn_action === 'send_email'){ ?>
                <button id="wcss-preview-email" data-id="<?php echo get_the_ID(); ?>" data-nonce="<?php echo wp_create_nonce("wcss_preview_email"); ?>" class="button">
                    <?php _e('Preview email', WCSS_SLUG); ?>
                </button>
            <?php } 
           
        } else {
            _e('This order is complete', WCSS_SLUG);
        }
    }

    public function after_acf_save_post($post_id) {
        if (!isset($_POST['wcss_actions']) || get_post_type($post_id) !== 'supplier_order') {
            return null;
        }
        switch ($_POST['wcss_actions']) {
        case 'send_email':
            $this->send_mail($post_id);
            break;
        case 'increase_stock':
            $this->increase_stock($post_id);
            break;
        case 'decrease_stock':
            $this->decrease_stock($post_id);
            break;
        default:
            break;
        }
    }

    private function send_mail($post_id) {
        $mail = new WCSS_Supplier_Mail($post_id);
        $success = $mail->send();
        if($success){
            update_field('order_status', 'sent', $post_id);
            update_field('_email_reminder', $mail->preview(), $post_id);
        }
    }

    private function increase_stock($post_id) {
        $order_items = get_field('order_items', $post_id);
        $stock_update = [];
        foreach($order_items as $item){
            $product = wc_get_product($item['product']);
            $quantity = $item['quantity'];
            $newQuantity  = $product->get_stock_quantity() + $quantity;
            // 1. Updating the stock quantity
            $stock_update[] = [
                'product' => $product->get_id(),
                'quantity' => $quantity
            ];
            update_post_meta($product->get_id(), '_stock', $newQuantity);
        }
        update_field('_stock_update', $stock_update, $post_id);
        update_field('order_status', 'complete', $post_id);
    }
    private function decrease_stock($post_id) {
        $stock_update = get_field('_stock_update', $post_id);
        foreach($stock_update as $item){
            $product = wc_get_product($item['product']);
            $quantity = $item['quantity'];
            $newQuantity  = $product->get_stock_quantity() - $quantity;
            // 1. Updating the stock quantity
            update_post_meta($product->get_id(), '_stock', $newQuantity);
        }
        update_field('order_status', 'sent', $post_id);
    }
    
}