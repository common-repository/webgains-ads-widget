<?php

function waw_init_db() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $ads_table = $wpdb->prefix . 'webgainsads';

    $sql_ads = "CREATE TABLE $ads_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            ad_id varchar(10) NOT NULL,
            ad_size varchar(10) NOT NULL,
            ad_script TEXT DEFAULT '' NOT NULL,
		    UNIQUE KEY id (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_ads);
}

function waw_remove_db() {
    global $wpdb;
    $ads_table = $wpdb->prefix . 'webgainsads';

    $wpdb->query("DROP TABLE IF EXISTS $ads_table");
}

function waw_insert_ads($ads_array) {
    global $wpdb;

    $ads_table = $wpdb->prefix . 'webgainsads';

    $wpdb->insert($ads_table, $ads_array);
}

function waw_get_ads($size, $limit, $adorder) {
    global $wpdb;
    $query = '';

    if ($adorder == 'Most Recent') {
        $query = sprintf(
                'SELECT ad_script FROM %s WHERE ad_size = "%s" LIMIT %s;', $wpdb->prefix . 'webgainsads', $size, $limit);
    } else if ($adorder == 'Random') {
        $query = sprintf(
                'SELECT ad_script FROM %s WHERE ad_size = "%s" ORDER BY RAND() LIMIT %s;', $wpdb->prefix . 'webgainsads', $size, $limit);
    }

    $ads = $wpdb->get_results($query, ARRAY_A);

    return $ads;
}

function waw_get_unique_ad_sizes() {
    global $wpdb;

    $query = sprintf(
            'SELECT DISTINCT(ad_size) AS ad_size FROM %s ORDER BY ad_size ASC;', $wpdb->prefix . 'webgainsads');

    $sizes = $wpdb->get_results($query, ARRAY_A);

    return $sizes;
}

function waw_do_empty_ad_table() {
    global $wpdb;

    $ads_table = $wpdb->prefix . 'webgainsads';

    $query = sprintf('TRUNCATE %s;', $ads_table);

    if (false === $wpdb->query($query)) {
        //log error
    }
}

function waw_exist_ads_by_id($ad_id) {
    global $wpdb;

    $query = sprintf(
            'SELECT id FROM %s WHERE ad_id = "%s"', $wpdb->prefix . 'webgainsads', $ad_id);

    $ads = $wpdb->get_row($query);

    return $ads == null ? false : true;
}

function waw_count_ad_table() {
    global $wpdb;

    $query = sprintf('SELECT COUNT(*) FROM %s;', $wpdb->prefix . 'webgainsads');

    $count = $wpdb->get_var($query);

    return $count;
}
