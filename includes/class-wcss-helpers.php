<?php
class WCSS_Helpers {
    public static function get_plugin_url(){
        return WCSS_URL;
    }
    public static function get_plugin_path(){
        return WCSS_PATH;
    }
    public static function ajax_url(){
        return admin_url( 'admin-ajax.php' );
    }
    public static function get_domain_name(){
        $info = parse_url($_SERVER['HTTP_HOST']);
        return $info['host'];
    }

    /**
     * Get ref from product object
     *
     * @param WC_Product $product
     * @return string
     */
    public static function get_product_ref($product){
        if($product->is_type('variation')){
            return get_post_meta($product->get_id(), WCSS_PREFIX . 'supplier_ref', true);
        }else{
            return get_field('supplier_ref', $product->get_id());
        }
    }
}
