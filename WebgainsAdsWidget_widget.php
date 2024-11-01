<?php
//Widget register
add_action('widgets_init', 'waw_widget_register');

function waw_widget_register() {
    register_widget('Webgains_Ad_Widget');
}

class Webgains_Ad_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
                // base ID of the widget
                'webgains-ad-widget-id',
                // name of the widget
                __('Webgains Ads Widget', 'webgains-ad-widget'),
                // widget options
                array(
            'description' => __('Displays Webgains Ads', 'webgains-ad-widget'),
                )
        );
    }

    public function form($instance) {
        //Set up some default widget settings.
        $defaults = ['adsize' => '', 'limit' => '0', 'onpost' => 'on', 'onpage' => 'on', 'adorder' => 'Most Recent'];

        $instance = wp_parse_args((array) $instance, $defaults);

        $sizes = waw_get_unique_ad_sizes();
        ?>
        <br />
        <fieldset>
            <label>Select ad size (WxH):</label>
            <select id="<?php echo $this->get_field_id('adsize'); ?>" name="<?php echo $this->get_field_name('adsize'); ?>" value="">
                <?php foreach ($sizes as $size) { ?>
                    <option <?php echo selected($instance['adsize'], $size['ad_size']); ?>
                        value="<?php echo $size['ad_size']; ?>"><?php echo $size['ad_size']; ?>
                    </option>
                <?php } ?>
            </select>
        </fieldset>
        <br />
        <fieldset>
            <label>Display</label>
            <input class="small-text" type="text"
                   style="text-align:center;"
                   id="<?php echo $this->get_field_id('limit'); ?>"
                   name="<?php echo $this->get_field_name('limit'); ?>"
                   value="<?php echo esc_attr($instance['limit']); ?>">
            <label>ads in</label>
            <select id="<?php echo $this->get_field_id('adorder'); ?>" name="<?php echo $this->get_field_name('adorder'); ?>">
                <option <?php selected($instance['adorder'], 'Most Recent'); ?> value="Most Recent">Most Recent</option>
                <option <?php selected($instance['adorder'], 'Random'); ?> value="Random">Random</option>
            </select>
            <label>order</label>
        </fieldset>
        <br />
        <fieldset>
            <input class="checkbox" type="checkbox" <?php checked($instance['onpost'], 'on'); ?> id="<?php echo $this->get_field_id('onpost'); ?>" name="<?php echo $this->get_field_name('onpost'); ?>" />
            <label style="padding-right: 10px;" for="<?php echo $this->get_field_id('onpost'); ?>">Show on Posts</label>
            <input class="checkbox" type="checkbox" <?php checked($instance['onpage'], 'on'); ?> id="<?php echo $this->get_field_id('onpage'); ?>" name="<?php echo $this->get_field_name('onpage'); ?>" />
            <label for="<?php echo $this->get_field_id('onpage'); ?>">Show on Pages</label>
        </fieldset>
        <br />
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['adsize'] = strip_tags($new_instance['adsize']);
        $instance['limit'] = strip_tags($new_instance['limit']);
        $instance['onpost'] = strip_tags($new_instance['onpost']);
        $instance['onpage'] = strip_tags($new_instance['onpage']);
        $instance['adorder'] = strip_tags($new_instance['adorder']);

        return $instance;
    }

    public function widget($args, $instance) {
        extract($args);

        $adsize = $instance['adsize'];
        $limit = $instance['limit'];
        $on_post = $instance['onpost'];
        $on_page = $instance['onpage'];
        $adorder = $instance['adorder'];

        $ads = waw_get_ads($adsize, $limit, $adorder);

        echo $args['before_widget'];

        if ($on_post == true && $on_page == true) {
            $this->display_ads($ads);
        } else if ($on_post == true && $on_page != true) {
            if (is_single()) {
                $this->display_ads($ads);
            }
        } else if ($on_post != true && $on_page == true) {
            if (!is_single()) {
                $this->display_ads($ads);
            }
        }

        echo $args['after_widget'];
    }

    public function display_ads($ads) {
        foreach ($ads as $ad) {
            echo $ad['ad_script'] . '<br />';
        }
    }

}
