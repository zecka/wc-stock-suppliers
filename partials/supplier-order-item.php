<?php
function wcss_get_supplier_item(WCSS_Supplier $supplier){
    ob_start(); 
    $products = $supplier->get_to_order_array();
    ?>
    <div class="supplier-item">
        <h3>#<?php echo $supplier->get_id(); ?> | <?php echo $supplier->get_title(); ?></h3>
        <h5><?php echo $supplier->get_email(); ?></h5>
        <?php if($pendings = $supplier->get_pending_orders()){ ?>
            <div class="supplier-item__error">
                <p>
                <?php _e('This supplier already have a pending order: Delete it or finish it.', WCSS_SLUG); ?>
                </p>
                <a href="<?php echo get_edit_post_link($pendings[0]->ID); ?>" class="button"><?php _e('Edit this pending supplier order', WCSS_SLUG);?></a>
            </div>
        <?php }else{ ?>
            <div class="supplier-item__toorder">
                <?php if($sent_orders = $supplier->get_email_sent_orders()){ ?>
                    <div class="supplier-item__error">
                        <p>
                        <?php _e('WARNING: This supplier already has an incomplete order. The email is already sent but the stock is not updated.', WCSS_SLUG); ?>
                        </p>
                        <a href="<?php echo get_edit_post_link($sent_orders[0]->ID); ?>" class="button"><?php _e('Edit this supplier order', WCSS_SLUG);?></a>
                    </div>
                <?php } ?>
                <?php if(isset($products[0]['ID'])): ?>
                    <table>
                        <thead>
                            <tr>
                            <?php foreach($products[0] as $key => $value): ?>
                                <th id="wc_supplier_<?php echo $key; ?>'"><?php echo $key ?></th>
                            <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $product): ?>
                                <tr>
                                    <?php foreach($product as $data): ?>
                                        <td><?php echo $data; ?></td>
                                    <?php endforeach;?>

                                <tr>
                            <?php endforeach;?>
                        </tbody>
                <?php endif; ?>
                </table>
            </div>
            <div class="supplier-item__action">
                <button 
                    class="button wcss-generate-order" 
                    data-nonce="<?php echo wp_create_nonce('wcss_generate_supplier_order'); ?>"
                    data-suppliers="<?php echo wp_json_encode([$supplier->get_id()]); ?>"
                >
                    Generate order
                </button>
            </div>
        <?php } ?>
    </div>
    <?php
    return ob_get_clean();
}