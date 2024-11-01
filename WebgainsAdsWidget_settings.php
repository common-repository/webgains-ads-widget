<?php
add_action('admin_menu', 'waw_add_settings_menu');

function waw_add_settings_menu() {
    add_options_page(
            'Webgains Ads Widget', 'Webgains Ads Widget', 'manage_options', 'webgains_ads_widget', 'waw_settings_display'
    );
}

function waw_settings_display() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have the sufficient permissons to access this page.', 'webgains-ads-widget'));
    }
    ?>

    <div class="wrap">
        <h2><?php _e('Webgains Ads Widget', 'webgains-ads-widget'); ?></h2>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <!-- main content -->
                <div id="post-body-content" class="postbox-container">
                    <div class="meta-box-sortables">
                        <div class="postbox">
                            <h3><span><?php
                                    esc_attr_e(
                                            'Webgains Account Details', 'webgains-ads-widget'
                                    );
                                    ?></span></h3>
                            <div class="inside">
                                <form method="post" action="options.php">
                                    <?php settings_fields('webgains_ads_widget'); ?>
                                    <?php do_settings_sections('webgains_ads_widget'); ?>
                                    <?php submit_button(); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="postbox-container-1" class="postbox-container">
                    <div class="meta-box-sortables">
                        <div class="postbox">
                            <h3><span><?php
                                    esc_attr_e(
                                            'Donations', 'webgains-ads-widget'
                                    );
                                    ?></span></h3>
                            <div class="inside">
                                <span><?php
                                    esc_attr_e(
                                            'If you like my plugin, please consider making a donation! '
                                            . 'Thank you.', 'webgains-ads-widget'
                                    );
                                    ?></span>
                                <center><br />
                                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                        <input type="hidden" name="cmd" value="_donations">
                                        <input type="hidden" name="business" value="ntuananh.2311@gmail.com">
                                        <input type="hidden" name="lc" value="NZ">
                                        <input type="hidden" name="item_name" value="Support Webgains Ads Widget plugin">
                                        <input type="hidden" name="no_note" value="0">
                                        <input type="hidden" name="currency_code" value="USD">
                                        <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
                                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                                    </form>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $options = get_option('webgains_ads_widget_settings');

                if (!empty($options['webgains_api_key']) && !empty($options['webgains_api_campaignid'])) {
                    ?>
                    <!-- sidebar -->
                    <div id="postbox-container-2" class="postbox-container">
                        <div class="meta-box-sortables">
                            <div class="postbox">
                                <h3><span><?php
                                        esc_attr_e(
                                                'Fetch Ads from Webgains', 'webgains-ads-widget'
                                        );
                                        ?></span></h3>
                                <div class="inside">
                                    <form method="post" action="<?php echo get_admin_url() . 'admin-post.php'; ?>">
                                        <input type='hidden' name='action' value='populate-submit-form' />
                                        <p>
                                            <input class='small-text' type='text' name='waw_populate_offset' value='0'/>
                                            <span class=''>Offset</span>
                                        </p>
                                        <p>
                                            <input class='small-text' type='text' name='waw_populate_limit' value='1000'/>
                                            <span class=''>Limit (max: 1000)</span>
                                        </p>
                                        <p>
                                            <input type="checkbox" name="waw_populate_joined" checked />
                                            <span class=''>Only fetch ads from joined programs</span>
                                        </p>

                                        <p>
                                            <?php echo submit_button('Fetch Ads', 'primary', 'waw_populate_ads', false); ?>
                                            <span class='description'>
                                                <?php
                                                esc_attr_e(
                                                        'Caution: this button will remove existing ads and replace with new ads as specified.', 'webgains-ads-widget'
                                                );
                                                ?>
                                            </span>
                                        </p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <br class="clear">
        </div>
    </div>
    <?php
}

add_action('admin_init', 'waw_init_options');

function waw_init_options() {
    add_settings_section(
            'waw_display_options_section', '', '', 'webgains_ads_widget'
    );

    add_settings_field(
            'webgains_api_url', 'Webgains API URL', 'waw_webgains_url_callback', 'webgains_ads_widget', 'waw_display_options_section'
    );

    add_settings_field(
            'webgains_api_key', 'Webgains API Key', 'waw_webgains_key_callback', 'webgains_ads_widget', 'waw_display_options_section'
    );

    add_settings_field(
            'webgains_api_campaignid', 'Webgains API Campaign ID', 'waw_webgains_campid_callback', 'webgains_ads_widget', 'waw_display_options_section'
    );

    register_setting(
            'webgains_ads_widget', 'webgains_ads_widget_settings', 'waw_ads_input_validation'
    );
}

function waw_ads_input_validation($input) {
    $old_option = get_option('webgains_ads_widget_settings');

    if (filter_var(trim($input['webgains_api_url'], FILTER_VALIDATE_URL)) === false) {
        $input['webgains_api_url'] = $old_option['webgains_api_url'];
        add_settings_error('webgains_api_url', 'waw_validate_num_tags_error', 'Settings were not saved: Webgains API URL is invalid', 'error');
    }

    if (empty($input['webgains_api_key']) || !ctype_alnum($input['webgains_api_key'])) {
        $input['webgains_api_key'] = $old_option['webgains_api_key'];
        add_settings_error('webgains_api_key', 'waw_validate_num_tags_error', 'Settings were not saved: Webgains API Key is empty or invalid', 'error');
    }

    if (empty($input['webgains_api_campaignid']) || !is_numeric($input['webgains_api_campaignid'])) {
        $input['webgains_api_campaignid'] = $old_option['webgains_api_campaignid'];
        add_settings_error('webgains_api_campaignid', 'waw_validate_num_tags_error', 'Settings were not saved: Webgains Campaign ID is empty or invalid', 'error');
    }

    $input['webgains_api_url'] = trim($input['webgains_api_url']);
    $input['webgains_api_key'] = sanitize_text_field($input['webgains_api_key']);
    $input['webgains_api_campaignid'] = sanitize_text_field($input['webgains_api_campaignid']);

    return $input;
}

function waw_webgains_url_callback() {
    $option_name = 'webgains_ads_widget_settings';
    $option_field = 'webgains_api_url';

    $options = get_option($option_name);

    $default_url = 'http://api.webgains.com/2.0/publisher/ads/';
    if (!empty($options[$option_field])) {
        $default_url = $options[$option_field];
    }

    echo sprintf('<input class="regular-text" type="text" name="%s[%s]" value="%s" />', $option_name, $option_field, $default_url);
    echo sprintf('<p class="description">%s</p>', __('Required', 'webgains-ads-widget'));
}

function waw_webgains_key_callback() {
    $option_name = 'webgains_ads_widget_settings';
    $option_field = 'webgains_api_key';

    $options = get_option($option_name);

    $default_key = '';
    if (!empty($options[$option_field])) {
        $default_key = $options[$option_field];
    }

    echo sprintf('<input class="regular-text" type="text" name="%s[%s]" value="%s" />', $option_name, $option_field, $default_key);
    echo sprintf('<p class="description">%s</p>', __('Required, only allow alphanumeric characters', 'webgains-ads-widget'));
}

function waw_webgains_campid_callback() {
    $option_name = 'webgains_ads_widget_settings';
    $option_field = 'webgains_api_campaignid';

    $options = get_option($option_name);

    $default_campaign_id = '';
    if (!empty($options[$option_field])) {
        $default_campaign_id = $options[$option_field];
    }

    echo sprintf('<input class="regular-text" type="text" name="%s[%s]" value="%s" />', $option_name, $option_field, $default_campaign_id);
    echo sprintf('<p class="description">%s</p>', __('Required, only allow numeric characters', 'webgains-ads-widget'));
}

add_action('admin_notices', 'waw_missing_webgains_admin_notice');

function waw_missing_webgains_admin_notice() {
    $options = get_option('webgains_ads_widget_settings');

    if (empty($options['webgains_api_key']) || empty($options['webgains_api_campaignid'])) {
        add_settings_error('general', 'settings_error', __('You have not fill in your Webgains API details.', 'webgains-ads-widget'), 'error');
    } else if (waw_count_ad_table() == 0) {
        add_settings_error('general', 'settings_error', __('You have not fetch any ads yet.', 'webgains-ads-widget'), 'error');
    }
}

/* function waw_webgains_joined_callback() {
  $options = get_option('webgains_ads_widget_settings');
  ?>
  <input type='checkbox' name='webgains_ads_widget_settings[only_joined_program]' <?php checked($options['only_joined_program'], 1); ?> value='1'>
  <span><?php esc_attr_e('', 'webgains-ads-widget'); ?></span>
  <?php
  } */
