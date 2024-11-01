<?php

/*
  Plugin Name: Webgains Ads Widget
  Plugin URI: http://wordpress.org/extend/plugins/webgains-ads-widget/
  Version: 1.5.1
  Author: Joseph Nguyen
  Description: Webgains Ads Widget gives you an ability to fetch ad units from Webgains API, and then display them on your site via widgets.
  Text Domain: webgains-ads-widget
  License: GPLv3
 */

require_once 'WebgainsAdsWidget_widget.php';
require_once 'WebgainsAdsWidget_db.php';
require_once 'WebgainsAdsWidget_settings.php';
require_once 'WebgainsAdsWidget_ads.php';


register_activation_hook(__FILE__, 'waw_activate');

function waw_activate() {
    waw_init_db();
}

register_deactivation_hook(__FILE__, 'waw_deactivate');

function waw_deactivate() {
    
}
