<?php
class WCSS_Supplier_Product {
    private $product;
    private $variation_id;
    private $prefix;
    private $manage_stock;
    private $stock;
    private $sku;
    private $stock_target;

    public function __construct($product, $variation_id){
        $this->product = $product;   
        $this->variation_id = $variation_id;
        $this->prefix = WCSS_PREFIX;
        $this->manage_stock = $product->managing_stock();
        $this->stock        = $product->get_stock_quantity();
        $this->sku          = $product->get_sku();
        if ($variation_id) {
            $this->stock_target = get_post_meta($variation_id, $this->prefix . 'min_stock_target', true);
        } else {
            $this->stock_target = get_field('min_stock_target', $this->product->get_id());
        }   
    }
    public function need_reorder(){
        return $this->stock_target > $this->stock;
    }
    public function get_qty_to_order(){
        return $this->stock_target - $this->stock;
    }
    public function get_name(){
        return $this->product->get_name();
    }
    public function get_id(){
        return $this->product->get_id();
    }
    public function to_array(){
        return [
            'ID'             => $this->product->get_id(),
            'title'          => $this->product->get_name(),
            'to_order'       => $this->get_qty_to_order(),
            'stock'          => $this->stock,
            'stock_target'   => $this->stock_target,
            'sku'            => $this->sku,
        ];
    }
    
}