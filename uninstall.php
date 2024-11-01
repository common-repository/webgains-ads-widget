<?php

// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

$option_name = 'webgains_ads_widget_settings';
$widget_option_name = 'widget_webgains-ad-widget-id';

if (get_option($option_name)) {
    delete_option($option_name);
}

if (get_option($widget_option_name)) {
    delete_option($widget_option_name);
}

require_once 'WebgainsAdsWidget_db.php';
waw_remove_db();
