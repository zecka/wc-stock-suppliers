<?php
class WCSS_Supplier {
    /** @var WCSS_Supplier_Product[] */
    private $to_order;
    /** @var array[] */
    private $to_order_array;
    /**
     * Supplier post_id
     *
     * @var int supplier ID post_id
     */
    private $supplier_id;
    /** @var string */
    private $prefix;
    /**
     * Constructor of WCSS_Supplier
     *
     * @param int $supplier ID of supplier (post_id)
     */
    public function __construct($supplier_id) {
        $this->to_order = [];
        $this->to_order_array = [];
        $this->supplier_id = $supplier_id;
        $this->prefix   = WCSS_PREFIX;
        $this->set_supplier_product_to_order();
    }

    /**
     * Check if this supplier have produc to reorder
     *
     * @return bool
     */
    public function have_products(){
        return (count($this->to_order_array) > 0);
    }

    public function get_orders_by_status($status){
         $orders = get_posts([
            'post_type' => 'supplier_order',
            'post_status' => ['publish', 'private'],
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'supplier',
                    'value' => $this->supplier_id,
                    'compare' => '=',
                ],
                [
                    'key' => 'order_status',
                    'value' => $status,
                    'compare' => 'IN',
                ]
            ]
        ]);

        return count($orders) > 0 ? $orders : false;
    }
    /**
     * Get pending order of this supplier
     *
     * @return WP_Post[]|bool
     */
    public function get_pending_orders(){
        return $this->get_orders_by_status('pending');
    }
    /**
     * Get order email sent for this supplier
     *
     * @return WP_Post[]|bool
     */
    public function get_email_sent_orders(){
        return $this->get_orders_by_status('sent');
    }

    /**
     * Set all products to be re-order from the supplier
     *
     * @return void
     */
    private function set_supplier_product_to_order() {
        $args = [
            'post_type'  => 'product',
            "meta_key"   => "supplier",
            "meta_value" => $this->supplier_id,
        ];
        $the_query = new WP_Query($args);

        if ($the_query->have_posts()):
            while ($the_query->have_posts()): $the_query->the_post();
                global $product;                
                if ($product->is_type('variable')) {
                    $this->add_variable_product_to_order($product);
                } else {
                    $this->add_product_to_order($product);
                }

            endwhile;
        endif;
    }

    private function add_product_to_order($product, $variation_id = false) {
        $product_order = new WCSS_Supplier_Product($product, $variation_id);
        if($product_order->need_reorder()){
            $this->to_order[] = $product_order;
            $this->to_order_array[] = $product_order->to_array();
        }
    }
    private function add_variable_product_to_order($product) {
        foreach ($product->get_available_variations() as $variation) {
            $variation_id = $variation['variation_id'];
            $variation_product = wc_get_product($variation_id);
            $this->add_product_to_order($variation_product, $variation['variation_id']);
        }
    }
    /**
     * Get title of supplier (supplier name)
     *
     * @return string
     */
    public function get_title(){
        return get_the_title($this->supplier_id);
    }
    /**
     * Get email of supplier
     *
     * @return string
     */
    public function get_email(){
        return get_field('email', $this->supplier_id);
    }
    /**
     * Get id of supplier
     *
     * @return int
     */
    public function get_id(){
        return $this->supplier_id;
    }
    /**
     * Undocumented function
     *
     * @return array[]
     */
    public function get_to_order_array(){
        return $this->to_order_array;
    }
    /**
     * Convert WCC_Supplier instance in array
     *
     * @return array
     */
    public function to_array() {
        return [
            'ID'       => $this->supplier_id,
            'email'    => $this->get_email(),
            'title'    => $this->get_title(),
            'to_order' => $this->to_order,
            'html'     => wcss_get_supplier_item($this),
        ];
    }
    /**
     * Create supplier_order (post_type) item with product to order of this supplier
     *
     * @return bool success
     */
    public function generate_order(){
        $postarr = array(
            'post_title'            => 'Order for '.$this->get_title(),
            'post_status'           => 'private',
            'post_type'             => 'supplier_order'
        );
        $post_id = wp_insert_post($postarr);
        if (is_wp_error($post_id)) {
            return false;
        }
        $order_items = [];
        foreach($this->to_order as $order_product){
            $order_items[] = [
                'product'   => $order_product->get_id(),
                'quantity'  => $order_product->get_qty_to_order(),
            ];
        }
        update_field('order_items', $order_items, $post_id);
        update_field('supplier', $this->get_id(), $post_id);
        update_field('order_status', 'pending', $post_id);
        return true;
    }
}