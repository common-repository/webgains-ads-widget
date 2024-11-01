<?php

add_action('admin_post_populate-submit-form', 'waw_handle_form_action'); // If the user is logged in
add_action('admin_post_nopriv_populate-submit-form', 'waw_handle_form_action'); // If the user in not logged in

/**
 * Handle populate ads button on Settings page
 */
function waw_handle_form_action() {
    //Check if any of the fields is null
    if (empty($_POST['waw_populate_ads']) || is_null($_POST['waw_populate_offset']) || is_null($_POST['waw_populate_limit'])) {
        return;
    }

    $offset = $_POST['waw_populate_offset'];
    $limit = $_POST['waw_populate_limit'];
    $joined = $_POST['waw_populate_joined'];

    //clear ad table before fetching new ads
    waw_do_empty_ad_table();

    //fetch new ads base on offet, limit and joined programs
    $ad_count = waw_do_fetch_ads($offset, $limit, $joined);

    //if some ads have been fetched, set the notification and go back to previous page
    if ($ad_count != 0) {
        add_settings_error('general', 'settings_updated', sprintf(__('%s ad units fetched.', 'webgains-ads-widget'), $ad_count), 'updated');
    } else {
        add_settings_error('general', 'settings_updated', sprintf(__('%s ad units fetched.', 'webgains-ads-widget'), $ad_count), 'error');
    }

    waw_go_back();
    exit;
}

/**
 * Do actual ad fetching work
 */
function waw_do_fetch_ads($offset, $limit, $joined) {
    $options = get_option('webgains_ads_widget_settings');

    $webgains_url = $options['webgains_api_url'];
    $webgains_key = $options['webgains_api_key'];
    $webgains_campid = $options['webgains_api_campaignid'];

    if (empty($webgains_url) || empty($webgains_key) || empty($webgains_campid)) {
        return;
    }

    $is_joined = 'not-joined';
    if (null != $joined) {
        $is_joined = 'joined';
    }

    $ads_filter = json_encode(array('media_type' => array('banner')));

    $url = sprintf('%s?key=%s&campaignId=%s&joined=%s&offset=%s&limit=%s&filters=%s', $webgains_url, $webgains_key, $webgains_campid, $is_joined, $offset, $limit, $ads_filter);

    $json = file_get_contents($url);
    if ($json === false) {
        add_settings_error('general', 'settings_updated', __('Something went wrong! please check your Webgains details and try again.', 'webgains-ads-widget'), 'error');
        waw_go_back();
        exit;
    }


    $datas = json_decode($json, true);

    $ad_count = 0;
    foreach ($datas as $data) {
        foreach ($data['data'] as $ad) {
            if (waw_exist_ads_by_id($ad['id']) == true) {
                continue;
            }

            $code = strtr($ad['code'], array('<\/script>' => '</script>'));
            $ads_array = array(
                'ad_id' => $ad['id'],
                'ad_size' => $ad['width'] . 'x' . $ad['height'],
                'ad_script' => $code,
            );

            waw_insert_ads($ads_array);

            $ad_count++;
        }
    }

    return $ad_count;
}

/**
 * Handle going back transition between pages
 */
function waw_go_back() {
    set_transient('settings_errors', get_settings_errors(), 30);
    $goback = add_query_arg('settings-updated', 'true', wp_get_referer());
    wp_redirect($goback);
}
