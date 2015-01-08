<?php
if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
delete_option('wppe_options');
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wpperecords");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wppenewsletter");
?>
