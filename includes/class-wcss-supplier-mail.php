<?php

class WCSS_Supplier_Mail{
    /** @var int */
    private $post_id;
    /** @var WP_Post */
    private $post;
    /** @var WP_Post */
    private $supplier;
    /** @var string */
    private $from_name;
    /** @var string */
    private $from_email;
    /** @var array */
    private $to;
    /** @var string|array */
    private $subject;
    /** @var string */
    private $reply_to_name;
    /** @var string */
    private $reply_to;
    /** @var string */
    private $cc_to;
    /** @var string */
    private $before_content;
     /** @var string */
    private $after_content;
    /** @var array */
    private $plugin_settings;
    private $custom_email;
    private $supplier_custom_email;
    private $headers;
    private $cell_style;
    public function __construct($post_id){
        $this->post_id      = $post_id;
        $this->post         = get_post($post_id);
        $this->custom_email = get_field('custom_email', $post_id);
        $this->supplier     = get_post(get_field('supplier', $post_id));
        $this->set_to();
        $this->supplier_custom_email = get_field('custom_email', $this->supplier);
        $this->plugin_settings = get_field('wcss_plugin_settings', 'options');
        $this->set_from_email();
        $this->set_from_name();
        $this->set_subject();
        $this->set_reply_to();
        $this->set_cc_to();
        $this->set_before_content();
        $this->set_after_content();
        $this->set_headers();
        $this->set_cell_style();
        
    }
    private function set_to(){
        $to = get_field('email', $this->supplier);
        $to = str_replace(" ", "", $to);
        $to = explode(",", $to);
        $this->to = $to;
    }
    private function set_from_email(){
        $this->from_email = $this->plugin_settings['from_email'];
    }
    private function set_from_name(){
        $this->from_name = $this->plugin_settings['from_name'];
    }
    private function set_cc_to(){
        if(isset($this->plugin_settings['cc_to']) && $this->plugin_settings['cc_to'] !== ""){
            $this->cc_to = $this->plugin_settings['cc_to'];
        }else{
            $this->cc_to = false;
        }
    }
    private function set_subject(){
        $this->subject = $this->plugin_settings['subject'];
        if ($this->supplier_custom_email && get_field('subject', $this->supplier)) {
            $this->subject = get_field('subject', $this->supplier);
        }
        if ($this->custom_email && get_field('subject', $this->post_id)) {
            $this->subject = get_field('subject', $this->post_id);
        }
        $this->subject = $this->apply_variables($this->subject);
    }

    private function set_reply_to(){
        $this->reply_to = ($this->plugin_settings['replyto_email']) ? $this->plugin_settings['replyto_email'] :  $this->from_email;
        $this->reply_to_name = ($this->plugin_settings['replyto_name']) ? $this->plugin_settings['replyto_name'] : $this->from_name;
    }
    private function set_before_content(){
        $this->before_content = $this->plugin_settings['before_content'];
        if ($this->supplier_custom_email && get_field('before_content', $this->supplier)) {
            $this->before_content = get_field('before_content', $this->supplier);
        }
        if ($this->custom_email && get_field('before_content', $this->post_id)) {
            $this->before_content = get_field('before_content', $this->post_id);
        }
        $this->before_content = $this->apply_variables($this->before_content);

    }
    private function set_after_content(){
        $this->after_content = $this->plugin_settings['after_content'];
        if ($this->supplier_custom_email && get_field('after_content', $this->supplier)) {
            $this->after_content = get_field('after_content', $this->supplier);
        }
        if ($this->custom_email && get_field('after_content', $this->post_id)) {
            $this->after_content = get_field('after_content', $this->post_id);
        }
        $this->after_content = $this->apply_variables($this->after_content);

    }
    private function apply_variables($content){
        $content = str_replace('%%title%%', get_the_title($this->supplier), $content);
        $content = str_replace('%%first_name%%', get_field('contact_firstname',$this->supplier), $content);
        $content = str_replace('%%last_name%%', get_field('contact_lastname',$this->supplier), $content);
        return $content;
    }
    public function html_content_type(){
        return 'text/html';
    }
    /**
     * Send email
     *
     * @return bool
     */
    public function send(){
        if($this->plugin_settings['disable_mail_sending']){
            return true;
        }
        if($this->to && $this->subject && $this->mail_content()){
            // Set content type
            add_filter( 'wp_mail_content_type', [$this, 'html_content_type']  );
            $mail = wp_mail(
                $this->to, 
                $this->subject, 
                $this->mail_content(), 
                $this->headers
            );

             // Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
            remove_filter( 'wp_mail_content_type', [$this, 'html_content_type'] );
            if($mail){
                return true;
            }
        }
        return false;
    }
    private function set_headers(){
        $headers = [];
        $user=wp_get_current_user();
        if($this->from_name){
            $name = $this->from_name;
        }else{
            $name = get_bloginfo('name');
        }
        // Important: Never change from address
        // "From" address must have the same domain as the wordpress site to prevent emails from being considered as spam.
        // Use reply-to instead
        $headers[] = 'From: '. $name.' <'.$this->from_email.'>'; 
        if($this->reply_to){
            $headers[] = 'Reply-To: '.$this->reply_to_name.' <'.$this->reply_to.'>'; 
        }else{
            if($user){
                $headers[] = 'Reply-To: '.$user->display_name.' <'.$user->user_email.'>'; 
            }
        }

        if($this->cc_to){
            $headers[] = 'Cc: '. $this->cc_to;
        }
        $this->headers = $headers;

    }

    private function mail_content(){
        ob_start();
        echo $this->before_content;
        $this->products_table();
        echo $this->after_content;
        return ob_get_clean();
    }
    public function preview(){
        $to = $this->to;
        ob_start();
        echo '<div style="padding: 10px">';
        echo 'Subject: '. $this->subject .'<br />';
        echo 'To: '. implode(", ", $to) .'<br />';
        foreach($this->headers as $header){
            echo  esc_html($header).'<br />';
        }
        echo $this->mail_content();
        echo '</div>';

        return ob_get_clean();

    }
    private function set_cell_style(){
        $this->cell_style = 'padding: 5px; border: 1px solid black;';
    }
    private function products_table(){
        $order_items = get_field('order_items', $this->post_id );
        ?>
        <table style="border-collapse:collapse">
            <thead>
                <th style="<?php echo $this->cell_style; ?>"><?php _e('REF', WCSS_SLUG); ?></th>
                <th style="<?php echo $this->cell_style; ?>"><?php _e('Title', WCSS_SLUG); ?></th>
                <th style="<?php echo $this->cell_style; ?>"><?php _e('Quantity', WCSS_SLUG); ?></th>
                <th style="<?php echo $this->cell_style; ?>"><?php _e('SKU', WCSS_SLUG); ?></th>
            </thead>
            <tbody>
                <?php
                foreach($order_items as $order_item ){
                    $this->order_item_row($order_item);
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    private function order_item_row($order_item){
        $product    = wc_get_product($order_item['product']);
        $sku        = ($product->get_sku()) ? $product->get_sku() : '';
        $title      = $product->get_name();
        $ref        = WCSS_Helpers::get_product_ref($product);
        $quantity   = $order_item['quantity'];
        ?>
        <tr>
            <td style="<?php echo $this->cell_style; ?>"><?php echo $ref; ?></td>
            <td style="<?php echo $this->cell_style; ?>"><?php echo $title; ?></td>
            <td style="<?php echo $this->cell_style; ?>"><?php echo $quantity; ?></td>
            <td style="<?php echo $this->cell_style; ?>"><?php echo $sku; ?></td>
        </tr>
        <?php
    }
}
