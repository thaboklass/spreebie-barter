<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// delete options from options table
delete_option('spreebie_barter_error_stage_children');
delete_option('spreebie_barter_ethereum_address');
delete_option('spreebie_barter_widget_text');
?>