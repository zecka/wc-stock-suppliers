<?php


class WCSS_Ajax{
    private $private_actions;
    public function __construct(){
        $this->private_actions = [];
    }
    public function run(){
        $this->register_ajax_actions();
    }
    private function register_ajax_actions(){
        $this->add_private_action('wcss_generate_supplier_order');
        $this->add_private_action('wcss_get_suppliers_stocks');
        $this->add_private_action('wcss_preview_email');
       
    }
    private function add_private_action($action){
        add_action('wp_ajax_'.$action, [$this, $action]);
        add_action('wp_ajax_nopriv_'.$action, [$this, $action]);
    }
    public function wcss_generate_supplier_order(){
        if(!$this->validate_ajax_action('wcss_generate_supplier_order')){
            die();
        }
        if(!isset($_POST['suppliers'])){
            wp_send_json_error(['message'=>'Please provide suppliers ids']);
        }

        foreach($_POST['suppliers'] as $supplier_id){
            $supplier = new WCSS_Supplier($supplier_id);
            $success = $supplier->generate_order();
            if(!$success){
                wp_send_json_error(['message'=>'Error during supplier order generation for supplier id:'.$supplier->get_id()]);
            }
        }
        wp_send_json_success();
        die();
    }
    public function wcss_preview_email(){
        if (!$this->validate_ajax_action('wcss_preview_email')) {
            die();
        }
        if(!isset($_POST['post_id'])){
            wp_send_json_error(['message'=>'You need to prive a post id', 'post_data'=>$_POST]);
        }
        $mail = new WCSS_Supplier_Mail($_POST['post_id']);
        wp_send_json_success(['preview' => $mail->preview()]);
    }
    public function wcss_get_suppliers_stocks(){
        if (!$this->validate_ajax_action('wcss_get_suppliers_stocks')) {
            die();
        }
        $paged =($_POST['paged']) ? $_POST['paged'] : 1;
        $suppliers_data = [];
        // https://gist.github.com/luetkemj/2023628
        $args = [ 
            'post_type'         => 'supplier',
            'posts_per_page'    => 5,
            'paged'             => $paged,
        ];
        $the_query = new WP_Query( $args );
        $max_num_pages = $the_query->max_num_pages;


        $i=0;
        if ( $the_query->have_posts() ) :
            while ( $the_query->have_posts() ) : $the_query->the_post();
                $supplier_order       = new WCSS_Supplier(get_the_ID());
                if($supplier_order->have_products()){
                    $suppliers_data[$i] = $supplier_order->to_array();
                    $i++;
                }
            endwhile;
        endif;
        wp_reset_query();


        wp_send_json_success([
            'suppliers'=>$suppliers_data,
            'max_num_pages' => $max_num_pages,
            'paged' => (int) $paged,
        ]);

        die();
    }

    public function private_action_error(){
        wp_send_json_error(['message'=>'you need to be logged to execute this action']);
    }
    private function validate_ajax_action($action){
        if ( !wp_verify_nonce($_POST['nonce'], $action)) {
            wp_send_json_error(['message' => 'Invalid Ajax Action']);
            die();
        }
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        } else {
            wp_send_json_error(['message' => 'Invalid Ajax Action']);
            die();
        }
    }

}