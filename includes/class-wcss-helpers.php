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
}
